CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);


CREATE TABLE Razas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_raza VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    imagen_url VARCHAR(255) NOT NULL
);


CREATE TABLE Caracteristicas_fisicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NULL,
    caracteristica VARCHAR(255) NULL
);

CREATE TABLE Raza_Caracteristica (
    id INT AUTO_INCREMENT PRIMARY KEY,
    raza_id INT NOT NULL,
    caracteristica_fisica_id INT NOT NULL,
    FOREIGN KEY (raza_id) REFERENCES Raza(id),
    FOREIGN KEY (caracteristica_fisica_id) REFERENCES Caracteristicas_fisicas(id)
);


CREATE TABLE Origen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NULL,
    origen VARCHAR(255) NULL
);

CREATE TABLE Raza_Origen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    raza_id INT NOT NULL,
    origen_id INT NOT NULL,
    FOREIGN KEY (raza_id) REFERENCES Raza(id),
    FOREIGN KEY (origen_id) REFERENCES Origen(id)
);