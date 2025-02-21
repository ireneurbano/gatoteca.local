from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from webdriver_manager.chrome import ChromeDriverManager
import mysql.connector

# Usar el webdriver de Chrome manualmente
service = Service('/path/to/chromedriver')  # Pon aquí la ruta de tu chromedriver
driver = webdriver.Chrome(service=service)

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

# Usamos el webdriver de Chrome
driver = webdriver.Chrome(ChromeDriverManager().install())

# URL de la página que queremos hacer scraping
url = "https://www.expertoanimal.com/razas-de-gatos.html"
driver.get(url)

# Esperar que la página cargue completamente
time.sleep(5)

# Buscar todos los contenedores de las razas de gatos
razas = driver.find_elements(By.CLASS_NAME, 'resultado.link')

# Iterar a través de las razas y extraer los datos necesarios
for raza in razas:
    try:
        # Extraer el nombre
        nombre_raza = raza.find_element(By.CLASS_NAME, 'titulo--resultado').text.strip()
        
        # Extraer la descripción
        descripcion = raza.find_element(By.CLASS_NAME, 'intro').text.strip()
        
        # Extraer la URL de la imagen
        imagen_url = raza.find_element(By.CLASS_NAME, 'imagen').get_attribute('src')
        
        # Insertar los datos en la base de datos
        cursor.execute('''
            INSERT INTO Razas (nombre_raza, descripcion, imagen_url)
            VALUES (%s, %s, %s)
        ''', (nombre_raza, descripcion, imagen_url))
        
        print(f"Raza: {nombre_raza}")
        print(f"Descripción: {descripcion}")
        print(f"Imagen: {imagen_url}")
        print('-' * 50)
        
    except Exception as e:
        print(f"Error al procesar una raza: {e}")

# Confirmar los cambios en la base de datos
conn.commit()

# Cerrar la conexión a la base de datos
cursor.close()
conn.close()

# Cerrar el navegador
driver.quit()
