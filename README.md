Location Distance Finder
A Laravel-based location application that uses Google Maps APIs to provide:

Google Places Autocomplete for Pickup and Drop location search
Google Distance Matrix API to calculate distance and estimated travel time
Google Maps Route View to display the route between locations


🛠️ Tech Stack

Backend: PHP 8.2, Laravel 12
Frontend: HTML, CSS, JavaScript
APIs: Google Maps JavaScript API, Google Places API, Google Distance Matrix API
Database: MySQL


✅ Requirements
Make sure you have the following installed:

PHP >= 8.2
Composer
MySQL (XAMPP or Laragon)
Google API Keys (Maps + Distance Matrix)


🚀 Installation & Setup
Step 1: Clone the Repository
bashgit clone https://github.com/Alok199810/location-app.git
cd location-app
Step 2: Install Dependencies
bashcomposer install
Step 3: Copy Environment File
bashcp .env.example .env

Windows users: use this instead:
cmdcopy .env.example .env

Step 4: Generate App Key
bashphp artisan key:generate
Step 5: Configure .env File
Open .env and update the following:
envAPP_NAME=LocationApp
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=location_app
DB_USERNAME=root
DB_PASSWORD=

GOOGLE_MAPS_KEY=your_google_maps_api_key_here
GOOGLE_MATRIX_KEY=your_google_distance_matrix_api_key_here
Step 6: Create Database

Open phpMyAdmin → http://localhost/phpmyadmin
Create a new database named location_app

Step 7: Run Migrations
bashphp artisan migrate
Step 8: Start the Server
bashphp artisan serve
Open your browser and go to:
http://127.0.0.1:8000

🔑 Google API Keys Setup
You need 2 API keys from Google Cloud Console:
KeyPurposeGOOGLE_MAPS_KEYMaps JavaScript API + Places AutocompleteGOOGLE_MATRIX_KEYDistance Matrix API
Enable these APIs in Google Cloud Console:

Go to APIs & Services Library
Enable Maps JavaScript API
Enable Places API
Enable Distance Matrix API


📸 Features
FeatureDescription🔍 AutocompleteGoogle Places Autocomplete on both Pickup & Drop fields📏 DistanceReal distance calculated via Distance Matrix API⏱️ Travel TimeEstimated driving time between locations🗺️ Route MapVisual route displayed on Google Map

📁 Project Structure
location-app/
├── app/
│   └── Http/
│       └── Controllers/
│           └── LocationController.php   # Main controller
├── resources/
│   └── views/
│       └── location.blade.php           # Main view (map + form)
├── routes/
│   └── web.php                          # App routes
├── .env.example                         # Environment template
└── README.md

🌐 Routes
MethodURLDescriptionGET/Main page with mapPOST/calculateCalculate distance via API

⚠️ Common Issues
REQUEST_DENIED error:

Make sure all 3 Google APIs are enabled in Cloud Console
Check API key restrictions are set to None for testing

ExpiredKeyMapError:

Your Google API key has expired — generate a new one

composer not recognized:

Download Composer from https://getcomposer.org
Make sure PHP is added to your system PATH

Sessions table not found:

Run php artisan migrate
Or set SESSION_DRIVER=file in .env


👨‍💻 Author

Name: Alok
Email: itsalok119@gmail.com



📄 License
This project is open-source and available under the MIT License.
