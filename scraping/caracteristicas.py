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

# Función para verificar si una característica ya existe en la base de datos
def caracteristica_existe(cursor, caracteristica):
    query = "SELECT COUNT(*) FROM Caracteristicas_fisicas WHERE caracteristica = %s"
    cursor.execute(query, (caracteristica,))
    return cursor.fetchone()[0] > 0

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

            print(f"Procesando nueva raza...")

            # Buscar el div correcto de "Características físicas"
            try:
                divs_genericos = driver.find_elements(By.CSS_SELECTOR, 'div.elemento.el--generic')
                caracteristicas_fisicas = set()  # Usamos un set para eliminar duplicados

                for div in divs_genericos:
                    try:
                        titulo = div.find_element(By.CSS_SELECTOR, 'div.titulo.titulo--infografia').text.strip()
                        if "Características físicas" in titulo:
                            caracteristicas_elemento = div.find_element(By.CSS_SELECTOR, 'ul')
                            for li in caracteristicas_elemento.find_elements(By.TAG_NAME, 'li'):
                                caracteristica_limpia = li.text.strip()
                                if caracteristica_limpia:  # Evitar valores vacíos
                                    caracteristicas_fisicas.add(caracteristica_limpia)
                    except Exception as e:
                        print(f"Error procesando un div de características físicas: {e}")

                if not caracteristicas_fisicas:
                    caracteristicas_fisicas.add("Características no disponibles")

                print(f"Características físicas encontradas: {caracteristicas_fisicas}")

                # Guardar cada característica en una fila diferente, evitando duplicados en la BD
                for caracteristica in caracteristicas_fisicas:
                    if not caracteristica_existe(cursor, caracteristica):  # Verificar si ya existe
                        query = "INSERT INTO Caracteristicas_fisicas (titulo, caracteristica) VALUES (%s, %s)"
                        cursor.execute(query, ("Características físicas", caracteristica))
                        conexion.commit()
                        print(f'Característica "{caracteristica}" guardada correctamente.')
                    else:
                        print(f'Característica "{caracteristica}" ya existe en la base de datos, omitiendo.')

            except Exception as e:
                print(f"Error al obtener las características físicas: {e}")

            # Cierra la pestaña actual y regresa a la pestaña principal
            driver.close()
            driver.switch_to.window(driver.window_handles[0])
        except Exception as e:
            print(f'Error al procesar la raza: {e}')
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
