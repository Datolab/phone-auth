# Phone Auth

Phone Auth is a project that provides phone authentication through SMS. It utilizes a backend built with vanilla PHP and a frontend developed in Elm, all orchestrated using Docker.

## Features

- Phone authentication via SMS
- Simple setup using Docker
- Clean architecture with separate services for backend, frontend, and database

## Technologies Used

- **Backend**: Vanilla PHP
- **Frontend**: Elm
- **Database**: MySQL
- **Web Server**: Nginx
- **Containerization**: Docker

## Installation

To set up the project locally, follow these steps:

1. **Clone the repository**:

   ```bash
   git clone https://github.com/Datolab/phone-auth.git
   ```

2. **Navigate into the project directory**:

   ```bash
   cd phone-auth
   ```

3. **Set up environment variables**: Create a `.env` file in the root directory and define the necessary environment variables. You can refer to the `docker-compose.yml` for the required variables.

4. **Run the application**:

   ```bash
   docker-compose up --build
   ```

## Usage

Once the application is running, you can access the following services:

- **Frontend**: Open your browser and navigate to `http://localhost:8001` to access the frontend.
- **Backend**: The backend service runs on port 9000 and is used for handling authentication requests.
- **Database**: MySQL is accessible on port 3306.
- **phpMyAdmin**: Access phpMyAdmin at `http://localhost:8080` for database management.

## Contributing

Contributions are welcome! Please follow these steps to contribute:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/YourFeature`)
3. Commit your changes (`git commit -m 'Add some feature'`)
4. Push to the branch (`git push origin feature/YourFeature`)
5. Open a pull request

## License

This project is licensed under the GPL 2 License. See the [LICENSE](LICENSE) file for details.

## Acknowledgments

- Thanks to the contributors and the open-source community for their support and resources.

