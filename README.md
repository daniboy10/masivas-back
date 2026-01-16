# Masivas Back (Laravel)

Incluye lógica de base de datos mediante Stored Procedures en MySQL.

Requisitos:
- PHP 8.4+
- Composer
- MySQL / MariaDB

Instalación local:

git clone https://github.com/daniboy10/masivas-back.git
cd masivas-back
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve

Stored Procedures utilizados en la base de datos:

DELIMITER $$

DROP PROCEDURE IF EXISTS procesar_carga_masiva$$
DROP PROCEDURE IF EXISTS obtener_personas_paginadas$$

CREATE PROCEDURE procesar_carga_masiva()
BEGIN
    DECLARE v_persona_id BIGINT;
    DECLARE v_done INT DEFAULT 0;
    DECLARE v_count INT;

    WHILE v_done = 0 DO
        SELECT COUNT(*) INTO v_count FROM datos_temp;

        IF v_count = 0 THEN
            SET v_done = 1;
        ELSE
            SELECT id INTO v_persona_id
            FROM (
                SELECT p.id
                FROM datos_temp dt
                LEFT JOIN persona p
                    ON p.nombre = dt.nombre
                   AND p.paterno = dt.paterno
                   AND (p.materno = dt.materno OR (p.materno IS NULL AND dt.materno IS NULL))
                LIMIT 1
            ) AS temp;

            IF v_persona_id IS NULL THEN
                INSERT INTO persona (nombre, paterno, materno, created_at, updated_at)
                SELECT nombre, paterno, materno, NOW(), NOW()
                FROM datos_temp
                LIMIT 1;

                SET v_persona_id = LAST_INSERT_ID();
            END IF;

            INSERT IGNORE INTO telefono (persona_id, telefono, created_at, updated_at)
            SELECT v_persona_id, telefono, NOW(), NOW()
            FROM datos_temp
            LIMIT 1;

            INSERT INTO direccion (
                persona_id, calle, numero_exterior, numero_interior, colonia, cp, created_at, updated_at
            )
            SELECT
                v_persona_id, calle, numero_exterior, numero_interior, colonia, cp, NOW(), NOW()
            FROM datos_temp
            LIMIT 1;

            DELETE FROM datos_temp LIMIT 1;
            SET v_persona_id = NULL;
        END IF;
    END WHILE;
END$$

CREATE PROCEDURE obtener_personas_paginadas(IN p_offset INT, IN p_limit INT)
BEGIN
    SELECT
        id,
        nombre,
        paterno,
        materno,
        CONCAT(nombre, ' ', paterno, ' ', IFNULL(materno, '')) AS nombre_completo,
        created_at,
        updated_at
    FROM persona
    ORDER BY id ASC
    LIMIT p_offset, p_limit;
END$$

DELIMITER ;

Ejecución:

CALL procesar_carga_masiva();
CALL obtener_personas_paginadas(0, 10);

Crear usuario administrador (Laravel Tinker):

php artisan tinker

php\App\Models\User::create([
    'name' => 'Administrador',
    'email' => 'admin@admin.com',
    'password' => bcrypt('admin123'),
    'tipo_usuario' => 'admin'
]);
