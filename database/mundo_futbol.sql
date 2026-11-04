CREATE DATABASE IF NOT EXISTS mundo_futbol
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE mundo_futbol;

CREATE TABLE IF NOT EXISTS mensajes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    nombre_completo VARCHAR(100)  NOT NULL,
    correo          VARCHAR(150)  NOT NULL,
    asunto          VARCHAR(150)  NOT NULL,
    mensaje         VARCHAR(2000) NOT NULL,

    fecha_registro  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    INDEX idx_fecha_registro (fecha_registro),

    CONSTRAINT chk_nombre_no_vacio  CHECK (CHAR_LENGTH(TRIM(nombre_completo)) > 0),
    CONSTRAINT chk_correo_formato   CHECK (correo LIKE '_%@_%._%'),
    CONSTRAINT chk_asunto_no_vacio  CHECK (CHAR_LENGTH(TRIM(asunto)) > 0),
    CONSTRAINT chk_mensaje_no_vacio CHECK (CHAR_LENGTH(TRIM(mensaje)) > 0)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
