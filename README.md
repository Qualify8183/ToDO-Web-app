# To-Do Lijst Applicatie met Gebruikersauthenticatie

## Beschrijving
Dit is een eenvoudige To-Do Lijst applicatie waar gebruikers zich kunnen registreren, inloggen, hun taken beheren en markeren als voltooid. Gebruikers kunnen taken toevoegen, bewerken, verwijderen en de prioriteit en de vervaldatum instellen. De applicatie ondersteunt ook een gebruikersauthenticatiesysteem, zodat alleen ingelogde gebruikers hun taken kunnen beheren.

## Functies
- **Gebruikersauthenticatie**: Gebruikers kunnen zich registreren en inloggen.
- **Taakbeheer**: Gebruikers kunnen taken aanmaken, bijwerken en verwijderen.
- **Taakprioriteit**: Taken kunnen worden gemarkeerd met een lage, gemiddelde of hoge prioriteit.
- **Voltooiingsstatus**: Taken kunnen als voltooid worden gemarkeerd en visueel onderscheiden worden.
- **Taakordening**: Taken worden gesorteerd op prioriteit en aanmaakdatum.
- **Gebruikersinfo-paneel**: Toont de naam van de ingelogde gebruiker met de optie om uit te loggen.

## Bestandsstructuur
Het project bestaat uit de volgende belangrijkste bestanden:

- **`index.php`**: Hoofdpagina die de taken van de ingelogde gebruiker toont en taakbeheer mogelijk maakt.
- **`login.php`**: Inlogpagina waar gebruikers zich kunnen aanmelden.
- **`register.php`**: Registratiepagina waar gebruikers een nieuw account kunnen aanmaken.
- **`logout.php`**: Verwerkt de uitlogactie door de sessie te vernietigen.
- **`db.php`**: Databaseverbinding en configuratie.
- **`README.md`**: Dit bestand.

## Database Structuur
De applicatie vereist een MySQL-database met ten minste de volgende tabellen:

1. **users** tabel:
   - `id` (INT, Primaire Sleutel, AUTO_INCREMENT)
   - `username` (VARCHAR)
   - `password` (VARCHAR)

2. **tasks** tabel:
   - `id` (INT, Primaire Sleutel, AUTO_INCREMENT)
   - `user_id` (INT, Buitenlandse Sleutel die verwijst naar `users(id)`)
   - `title` (VARCHAR)
   - `description` (TEXT)
   - `priority` (ENUM: 'Low', 'Medium', 'High')
   - `due_date` (DATETIME)
   - `is_completed` (BOOLEAN, Standaard: 0)
   - `created_at` (DATETIME)

## Vereisten
- PHP (Aanbevolen versie 7.4 of hoger)
- MySQL Database
- Webserver (Apache, Nginx, enz.)

## Installatie

1. **Clone of Download de Repository**:
   ```bash
   git clone https://github.com/jegebruikersnaam/todo-app.git
   cd todo-app
   ```

2. **Database Instellen**:
   - Maak een database aan in MySQL.
   - Importeer de database structuur door de volgende SQL-commando's uit te voeren:

   ```sql
   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       username VARCHAR(255) NOT NULL,
       password VARCHAR(255) NOT NULL
   );

   CREATE TABLE tasks (
       id INT AUTO_INCREMENT PRIMARY KEY,
       user_id INT NOT NULL,
       title VARCHAR(255) NOT NULL,
       description TEXT NOT NULL,
       priority ENUM('Low', 'Medium', 'High') NOT NULL,
       due_date DATETIME NOT NULL,
       is_completed BOOLEAN DEFAULT 0,
       created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (user_id) REFERENCES users(id)
   );
   ```

3. **Databaseverbinding Configureren**:
   - Bewerk het bestand `db.php` om je MySQL-inloggegevens in te vullen (host, gebruikersnaam, wachtwoord en databasenaam).

   ```php
   <?php
   $host = 'localhost';  // of je database host
   $db = 'je_database_naam';
   $user = 'je_database_gebruiker';
   $pass = 'je_database_wachtwoord';

   try {
       $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
       $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   } catch (PDOException $e) {
       die("Kan geen verbinding maken met de database $db :" . $e->getMessage());
   }
   ?>
   ```

4. **Start de Server**:
   - Als je de ingebouwde PHP-server gebruikt:
     ```bash
     php -S localhost:8000
     ```

5. **Toegang tot de Applicatie**:
   - Open je browser en ga naar `http://localhost:8000` (of de host/poort die je server gebruikt).

## Gebruik

### Gebruiker Registreren
- Ga naar de pagina `register.php`.
- Vul een gebruikersnaam en wachtwoord in om een nieuw account aan te maken.

### Inloggen
- Na registratie, ga naar de pagina `login.php`.
- Vul je gebruikersnaam en wachtwoord in om in te loggen.

### Taken Beheren
- Zodra je bent ingelogd, word je doorgestuurd naar de pagina `index.php` waar je:
  - **Een taak toevoegen**: Klik op "Nieuwe taak toevoegen".
  - **Taken bewerken/verwijderen**: Taken kunnen bewerkt of verwijderd worden door de respectieve knoppen in te drukken.
  - **Taken markeren als voltooid**: Taken kunnen als voltooid gemarkeerd worden.
  - **Uitloggen**: Klik op de knop "Uitloggen" in het gebruikersinfo-paneel.

## Gebruikte Technologieën
- **PHP** voor backend logica.
- **MySQL** voor databasebeheer.
- **Bootstrap** voor front-end styling.
- **HTML/CSS** voor paginaopmaak en ontwerp.

## Licentie
Dit project is open-source en beschikbaar onder de [MIT Licentie](https://opensource.org/licenses/MIT).
