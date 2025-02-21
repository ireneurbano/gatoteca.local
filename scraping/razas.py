import requests
from bs4 import BeautifulSoup
import mysql.connector

# Configuración de la base de datos
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="usuario",
    database="gatoteca"
)

cursor = db.cursor()

# URL de la página que quieres hacer scraping
url = "https://www.expertoanimal.com/razas-de-gatos.html"

# Realiza la solicitud HTTP a la página
response = requests.get(url)
soup = BeautifulSoup(response.content, "html.parser")

# Encuentra todas las razas de gatos
razas = soup.find_all("div", class_="resultado link")

# Itera sobre cada raza y extrae los datos
for raza in razas:
    nombre = raza.find("a", class_="titulo").text.strip()
    descripcion = raza.find("div", class_="intro").text.strip()
    
    # Encuentra la URL de la imagen (en este caso el primer <img>)
    imagen_url = raza.find("img")["src"]

    # Imprime los datos para verificar
    print(f"Nombre: {nombre}")
    print(f"Descripción: {descripcion}")
    print(f"Imagen URL: {imagen_url}")
    
    # Inserta los datos en la base de datos
    query = """
    INSERT INTO Razas (nombre_raza, descripcion, imagen_url)
    VALUES (%s, %s, %s)
    """
    cursor.execute(query, (nombre, descripcion, imagen_url))

# Confirma los cambios y cierra la conexión
db.commit()
cursor.close()
db.close()
