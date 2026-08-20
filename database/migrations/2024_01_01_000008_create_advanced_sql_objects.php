<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // -----------------------------------------------------------------
        // 1. SQL VIEW: v_jadwal_publik
        // Menggabungkan pertandingan + turnamen + tim (tim_1 & tim_2)
        // untuk endpoint jadwal publik (tanpa perlu N+1 query di aplikasi).
        // -----------------------------------------------------------------
        DB::unprepared('DROP VIEW IF EXISTS v_jadwal_publik');
        DB::unprepared('
            CREATE VIEW v_jadwal_publik AS
            SELECT
                p.id_pertandingan,
                p.id_turnamen,
                t.nama_turnamen,
                p.babak,
                p.id_tim_1,
                tim1.nama_tim  AS nama_tim_1,
                p.id_tim_2,
                tim2.nama_tim  AS nama_tim_2,
                p.skor_1,
                p.skor_2,
                p.id_tim_pemenang,
                timw.nama_tim  AS nama_tim_pemenang,
                p.status_pertandingan,
                t.tanggal
            FROM pertandingan p
            INNER JOIN turnamen t   ON t.id_turnamen = p.id_turnamen
            LEFT JOIN tim tim1      ON tim1.id_tim = p.id_tim_1 AND tim1.deleted_at IS NULL
            LEFT JOIN tim tim2      ON tim2.id_tim = p.id_tim_2 AND tim2.deleted_at IS NULL
            LEFT JOIN tim timw      ON timw.id_tim = p.id_tim_pemenang AND timw.deleted_at IS NULL
        ');

        // -----------------------------------------------------------------
        // 2. TRIGGER: after_pendaftaran_update
        // Jika status_pendaftaran diubah menjadi 'disetujui' dan jumlah
        // tim yang disetujui pada turnamen tsb sudah mencapai kuota_maksimal,
        // otomatis ubah status_turnamen menjadi 'berlangsung'.
        // -----------------------------------------------------------------
        DB::unprepared('DROP TRIGGER IF EXISTS after_pendaftaran_update');
        DB::unprepared("
            CREATE TRIGGER after_pendaftaran_update
            AFTER UPDATE ON pendaftaran
            FOR EACH ROW
            BEGIN
                DECLARE v_total_disetujui INT;
                DECLARE v_kuota_maksimal INT;

                IF NEW.status_pendaftaran = 'disetujui'
                   AND (OLD.status_pendaftaran IS NULL OR OLD.status_pendaftaran <> 'disetujui') THEN

                    SELECT COUNT(*) INTO v_total_disetujui
                    FROM pendaftaran
                    WHERE id_turnamen = NEW.id_turnamen
                      AND status_pendaftaran = 'disetujui';

                    SELECT kuota_maksimal INTO v_kuota_maksimal
                    FROM turnamen
                    WHERE id_turnamen = NEW.id_turnamen;

                    IF v_total_disetujui >= v_kuota_maksimal THEN
                        UPDATE turnamen
                        SET status_turnamen = 'berlangsung'
                        WHERE id_turnamen = NEW.id_turnamen
                          AND status_turnamen = 'buka';
                    END IF;
                END IF;
            END
        ");

        // -----------------------------------------------------------------
        // 3. STORED PROCEDURE: generate_bracket
        // Kerangka awal logic pengocokan bracket (matchmaking).
        // Implementasi detail (single elimination, seeding, dsb.) dapat
        // dikembangkan lebih lanjut di dalam prosedur ini.
        // -----------------------------------------------------------------
        DB::unprepared('DROP PROCEDURE IF EXISTS generate_bracket');
        DB::unprepared("
            CREATE PROCEDURE generate_bracket(IN p_id_turnamen INT)
            BEGIN
                DECLARE v_done INT DEFAULT 0;
                DECLARE v_id_tim INT;
                DECLARE v_tim_lawan INT DEFAULT NULL;
                DECLARE cur_tim CURSOR FOR
                    SELECT tm.id_tim
                    FROM pendaftaran pd
                    INNER JOIN tim tm ON tm.id_tim = pd.id_tim
                    WHERE pd.id_turnamen = p_id_turnamen
                      AND pd.status_pendaftaran = 'disetujui'
                    ORDER BY RAND();
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;

                -- Hapus bracket lama (babak awal) sebelum generate ulang.
                DELETE FROM pertandingan
                WHERE id_turnamen = p_id_turnamen
                  AND babak = 'Babak Awal';

                OPEN cur_tim;

                read_loop: LOOP
                    FETCH cur_tim INTO v_id_tim;
                    IF v_done = 1 THEN
                        LEAVE read_loop;
                    END IF;

                    IF v_tim_lawan IS NULL THEN
                        SET v_tim_lawan = v_id_tim;
                    ELSE
                        INSERT INTO pertandingan
                            (id_turnamen, babak, id_tim_1, id_tim_2, status_pertandingan, created_at, updated_at)
                        VALUES
                            (p_id_turnamen, 'Babak Awal', v_tim_lawan, v_id_tim, 'menunggu', NOW(), NOW());
                        SET v_tim_lawan = NULL;
                    END IF;
                END LOOP;

                -- Jika jumlah tim ganjil, tim terakhir mendapat 'bye' (lawan kosong).
                IF v_tim_lawan IS NOT NULL THEN
                    INSERT INTO pertandingan
                        (id_turnamen, babak, id_tim_1, id_tim_2, status_pertandingan, created_at, updated_at)
                    VALUES
                        (p_id_turnamen, 'Babak Awal', v_tim_lawan, NULL, 'menunggu', NOW(), NOW());
                END IF;

                CLOSE cur_tim;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS generate_bracket');
        DB::unprepared('DROP TRIGGER IF EXISTS after_pendaftaran_update');
        DB::unprepared('DROP VIEW IF EXISTS v_jadwal_publik');
    }
};
