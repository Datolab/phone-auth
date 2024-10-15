#!/bin/bash

# Load environment variables
export $(grep -v '^#' backend/.env | xargs)

# Setup Database
mysql -u $DB_USER -p$DB_PASSWORD -h $DB_HOST < database/schema.sql

# Install Composer dependencies
cd backend
composer install

# Compile Elm
cd ../frontend/elm
elm make src/Main.elm --output=../public/js/main.js

echo "Setup Complete!"