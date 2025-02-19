# FinanzasCDG

FinanzasCDG is a web application designed to manage church finances, including member registration, contribution tracking, and report generation. The project is built using PHP and includes various modules to handle different aspects of the financial management process.

## Achieved Objectives

### Modules

1. **Dashboard**
   - Provides an overview of the application and quick access to various functionalities.
   - File: [`index.php`](index.php)

2. **Member Registration**
   - Allows the registration of new members.
   - Files: [`miembros/registrar.php`](miembros/registrar.php), [`miembros/eliminar.php`](miembros/eliminar.php), [`miembros/editar.php`](miembros/editar.php), [`miembros/listado.php`](miembros/listado.php)

3. **Contribution Registration**
   - Enables the registration of contributions made by members.
   - Files: [`transacciones/registrar.php`](transacciones/registrar.php), [`transacciones/historial.php`](transacciones/historial.php)

4. **Report Generation**
   - Generates financial reports based on the contributions and other financial data.
   - Files: [`informes/generar.php`](informes/generar.php), [`informes/generar_pdf.php`](informes/generar_pdf.php)

5. **User Authentication**
   - Manages user login and logout functionalities.
   - Files: [`login.php`](login.php), [`logout.php`](logout.php), [`includes/user_auth.php`](includes/user_auth.php)

6. **Database Connection**
   - Handles the connection to the database using PDO.
   - File: [`includes/db.php`](includes/db.php)

7. **PDF Generation**
   - Utilizes TCPDF library to generate PDF documents.
   - Files: [`includes/tcpdf/tcpdf.php`](includes/tcpdf/tcpdf.php), [`includes/tcpdf/tcpdf_autoconfig.php`](includes/tcpdf/tcpdf_autoconfig.php), [`includes/tcpdf/config/tcpdf_config.php`](includes/tcpdf/config/tcpdf_config.php)

## Pending Objectives

1. **Data Validation**
   - Implement comprehensive data validation across all forms to ensure data integrity and prevent errors.

2. **Bug Fixes in Contribution Registration Module**
   - Address and resolve any existing bugs in the contribution registration process to ensure smooth functionality.

3. **Bug Fixes in Member Registration Module**
   - Identify and fix bugs in the member registration module to improve user experience and reliability.

## Installation

1. Clone the repository to your local machine.
2. Set up a web server (e.g., XAMPP) and place the project files in the server's root directory.
3. Create a database and import the provided SQL file to set up the necessary tables.
4. Update the database connection details in [`includes/db.php`](includes/db.php).
5. Start the web server and navigate to the project URL to access the application.

## Usage

1. Open the application in your web browser.
2. Log in using your credentials.
3. Use the sidebar menu to navigate through different modules such as member registration, contribution registration, and report generation.
4. Follow the on-screen instructions to perform various tasks.

## Contributing

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Commit your changes and push them to your forked repository.
4. Create a pull request to merge your changes into the main repository.

