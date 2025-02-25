import time
import pymysql
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

# Función para conectar con la base de datos MySQL usando pymysql
def conectar_db():
    db_config = {
        'host': 'localhost',
        'user': 'root',
        'password': 'usuario',
        'database': 'gatoteca',
    }
    print("Conectando a la base de datos...")
    return pymysql.connect(**db_config)

# Función para configurar el driver de Selenium
def configurar_driver():
    print("Configurando el driver de Selenium...")
    servicio = Service('/usr/bin/chromedriver')  # Ruta correcta a chromedriver
    opciones = Options()
    return webdriver.Chrome(service=servicio, options=opciones)

# Función para obtener los datos de las razas
def obtener_datos():
    # Configura el driver
    driver = configurar_driver()

    # URL del sitio web con las razas
    url = 'https://www.expertoanimal.com/razas-de-gatos.html'
    print(f"Accediendo a la URL: {url}")
    driver.get(url)

    # Espera para que la página cargue
    print("Esperando a que la página cargue...")
    WebDriverWait(driver, 15).until(
        EC.presence_of_element_located((By.CSS_SELECTOR, 'div.resultado a'))
    )

    # Encuentra todos los enlaces a las razas
    print("Buscando enlaces a las razas...")
    razas = driver.find_elements(By.CSS_SELECTOR, 'div.resultado a')

    # Conectar a la base de datos
    conexion = conectar_db()
    cursor = conexion.cursor()

    # Itera sobre los resultados y guarda los datos en la base de datos
    for raza in razas:
        try:
            # Abre cada enlace de raza en una nueva pestaña y cambia de pestaña
            link = raza.get_attribute('href')
            driver.execute_script("window.open(arguments[0]);", link)
            driver.switch_to.window(driver.window_handles[1])

            WebDriverWait(driver, 15).until(
                EC.presence_of_element_located((By.CSS_SELECTOR, 'h1'))
            )

            nombre = driver.find_element(By.CSS_SELECTOR, 'h1').text
            print(f"Nombre de la raza: {nombre}")

            # Verificar si existe la descripción
            try:
                descripcion = driver.find_element(By.CSS_SELECTOR, 'div.intro p').text
                print(f"Descripción encontrada: {descripcion}")
            except:
                descripcion = "Descripción no disponible"
                print("Descripción no disponible")

            # Verificar si existe la imagen
            try:
                imagen_url = driver.find_element(By.CSS_SELECTOR, 'div.intro div.imagen img').get_attribute('src')
                print(f"URL de la imagen: {imagen_url}")
            except:
                imagen_url = "Imagen no disponible"
                print("Imagen no disponible")

            # Inserta los datos en la base de datos
            query = "INSERT INTO Razas (nombre_raza, descripcion, imagen_url) VALUES (%s, %s, %s)"
            cursor.execute(query, (nombre, descripcion, imagen_url))
            conexion.commit()

            print(f'Raza {nombre} guardada correctamente.')

            # Cierra la pestaña actual y regresa a la pestaña principal
            driver.close()
            driver.switch_to.window(driver.window_handles[0])
        except Exception as e:
            print(f'Error al procesar la raza: {e}')
            # Si hay un error, cierra la pestaña actual y regresa a la pestaña principal
            driver.close()
            driver.switch_to.window(driver.window_handles[0])

    # Cierra la conexión y el driver
    print("Cerrando la conexión a la base de datos...")
    cursor.close()
    conexion.close()
    driver.quit()

# Llamada a la función para obtener los datos
print("Iniciando la obtención de datos...")
obtener_datos()
print("Proceso completado.")
