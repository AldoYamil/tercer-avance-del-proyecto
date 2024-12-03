index (main page): 
header
navbar 
 >home  //Boton que redirecciona al inicio de la pagina
 >services // Boton que desplace al usuario hasta los servicios
 >how it works // Boton que desplace al usuario hasta la explicacion de como funciona nuestro sistema
 >testimonials // Boton que desplace al usuario hasta la zona de testimonios
 >news & updates // Boton que desplace al usuario hasta el blog
 >contact //Reenvio a un formulario
 >login //Despliegue de ambos logins
  -client login //Login de cliente con opcion a registrarse
  -employee login //Login de empleado sin opcion a registrarse
 >language // Menu desplegable con idioma espanol e ingles
main content
 >services// Los servicios que ofrecemos
  -distribution //Explicacion breve sobre la distribucion
  -delivery //Explicacion breve sobre las entregas
  -tracking //Explicacion breve sobre como se rastrean los pedidos
 >how it works//Explicacion detallada de como funciona en general nuestro sistema
 >testimonials//Zona de testimonios
 >news & updates//Blog actualizable sobre noticias y actualizacionces
footer
 >social media links //Iconos que reedireccionen a redes social
 >privacy policy //Texto clickeable que reedireccione a politicas de privacidad (Generica)
 >terms of service //Texto clickeable que reedireccione a terminos de servicio (Generico)
 /project-root
|
|-- /app                    # Contains all the controller logic
|   |-- /controllers         # Controllers handle app logic and interactions
|       |-- AuthController.php        # Handles login, registration, logout, and session management
|       |-- AdminController.php       # Handles admin-specific functionalities
|       |-- ClientController.php      # Handles client-specific functionalities
|   |
|-- /data                    # Contains database connections and models
|   |-- /config              # Configuration for the database
|       |-- database.php          # Database connection setup
|       |-- config.php            # General configuration settings (e.g., env vars)
|   |-- /models              # Classes representing database entities and their methods
|       |-- User.php              # User class with properties, getters, setters, and DB operations
|       |-- Package.php           # Package class for managing shipments
|       |-- Admin.php             # Admin class for managing admins
|       |-- Client.php            # Client class for client-specific operations
|       |-- Employee.php          # Employee class for employee-specific operations
|       |-- etc...
|-- /public                  # Public directory (accessible from the browser)
|   |-- /assets              # Static assets (CSS, images, etc.)
|       |-- /css                 # Stylesheets
|           |-- style.css            # Main CSS file
|       |-- /images              # Images used in the website
|   |-- index.php              # The main entry point of the application (front controller)
|
|-- /views                   # Contains all the HTML files (view layer)
|   |-- /auth                 # Authentication-related views
|       |-- login.php             # Login form for all users
|       |-- register.php          # Registration form for clients
|   |-- /admin                # Views related to the admin dashboard
|       |-- dashboard.php         # Admin dashboard view
|       |-- manageUsers.php       # View for managing users
|       |-- assignPackage.php     # View for assigning packages to employees
|   |-- /client               # Views related to client interactions
|       |-- dashboard.php         # Client dashboard view
|       |-- trackPackage.php      # View for tracking package status
|       |-- profile.php           # View for client profile settings
|
|-- /routes                  # Routes for handling HTTP requests and directing to controllers
|   |-- web.php                  # Routing for all app routes (e.g., /login, /admin/dashboard)
|
|-- /Docs                  # Documentation for the project
|   |-- activities.md         # Activities for the project
|   |-- structure.md          # Structure for the project
|   |-- methodology.md       # Methodology for the project
|-- README.md                 # Documentation for the project