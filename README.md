# Phone Auth

Phone Auth is a project that provides phone authentication through SMS with JWT-based authorization for enhanced security. It features a backend built with vanilla PHP and a frontend developed in Elm, all orchestrated using Docker.

## Features

- Phone authentication via SMS with OTP (One-Time Password) verification
- JWT-based authentication for secure, platform-independent token management
- Cross-Origin Resource Sharing (CORS) support for API requests from the frontend
- Environment-based configuration for flexible deployment setups
- Simple setup using Docker for seamless container orchestration
- Clean architecture with separate services for backend, frontend, and database

## Technologies Used

- **Backend**: Vanilla PHP, JWT for authentication
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

3. **Set up environment variables**: Create a `.env` file in the root directory and define the necessary environment variables. Use `.env.dist` as a reference. Set the following environment variables:

   - **Database**: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`
   - **Twilio SMS**: `TWILIO_ACCOUNT_SID`, `TWILIO_SERVICE_SID`, `TWILIO_AUTH_TOKEN`, `TWILIO_PHONE_NUMBER`
   - **Telegram (Optional)**: `TELEGRAM_BOT_NAME`, `TELEGRAM_BOT_TOKEN`, `TELEGRAM_LOGIN_URL`
   - **Application**:
     - `APP_BASE_URL`: The base URL of the app.
     - `ALLOWED_ORIGIN`: Frontend URL for CORS.
     - **JWT Configuration**: `JWT_SECRET_KEY` for signing JWTs and `JWT_EXPIRATION` for token expiration time (in seconds).

4. **Run the application**:

   ```bash
   docker-compose up --build
   ```

## Usage

Once the application is running, you can access the following services:

- **Frontend**: Open your browser and navigate to `http://localhost:8001` to access the frontend.
- **Backend**: The backend service runs on port 9000 and handles authentication requests, including:
  - `/auth/sms`: Initiates the SMS authentication process.
  - `/auth/verify`: Verifies the OTP and returns a JWT on success.
  - `/auth/validate`: Checks the validity of a provided JWT.
- **Database**: MySQL is accessible on port 3306.
- **phpMyAdmin**: Access phpMyAdmin at `http://localhost:8080` for database management.

### Authentication Flow

1. **Send OTP**: The client calls `/auth/sms` with the phone number to receive an OTP via SMS.
2. **Verify OTP**: The client submits the OTP to `/auth/verify`. Upon successful verification, the server responds with a JWT.
3. **JWT Validation**: For protected routes, the client includes the JWT in the `Authorization` header as `Bearer <token>`. The server validates the token for access.

## Security

- **JWT Authentication**: Ensures secure and stateless authentication, allowing platform-independent access management.
- **CORS Headers**: Configured to restrict API access to specific origins, set via `ALLOWED_ORIGIN` in `.env`.
- **Token Expiration**: The `JWT_EXPIRATION` environment variable sets the expiration duration of JWTs, enhancing security by limiting token validity.

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