import requests
from bs4 import BeautifulSoup
import mysql.connector

# Conexión a la base de datos MySQL
conn = mysql.connector.connect(
    host="localhost",     # Cambia esto según tu configuración
    user="root",          # Tu usuario de base de datos
    password="usuario",   # Tu contraseña de base de datos
    database="gatoteca"   # El nombre de tu base de datos
)

cursor = conn.cursor()

# Crear la tabla "razas" si no existe
cursor.execute('''
    CREATE TABLE IF NOT EXISTS Razas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre_raza VARCHAR(255) NOT NULL,
        descripcion TEXT NOT NULL,
        imagen_url VARCHAR(255) NOT NULL
    )
''')

# URL de la página que queremos hacer scraping
url = "https://www.expertoanimal.com/razas-de-gatos.html"

# Obtener el contenido de la página
response = requests.get(url)
soup = BeautifulSoup(response.text, 'html.parser')

# Buscar todos los contenedores de las razas de gatos
razas = soup.find_all('div', class_='resultado link')

# Iterar a través de las razas y extraer los datos necesarios
for raza in razas:
    nombre_raza = raza.find('a', class_='titulo titulo--resultado').text.strip()
    descripcion = raza.find('div', class_='intro').text.strip()
    imagen_url = raza.find('img', class_='imagen')['src']

    # Insertar los datos en la base de datos
    cursor.execute('''
        INSERT INTO Razas (nombre_raza, descripcion, imagen_url)
        VALUES (%s, %s, %s)
    ''', (nombre_raza, descripcion, imagen_url))

    print(f"Raza: {nombre_raza}")
    print(f"Descripción: {descripcion}")
    print(f"Imagen: {imagen_url}")
    print('-' * 50)

# Confirmar los cambios en la base de datos
conn.commit()

# Cerrar la conexión a la base de datos
cursor.close()
conn.close()
