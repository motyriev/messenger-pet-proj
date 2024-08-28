This pet-project is a microservice-based messenger application. It features real-time communication using WebSockets and is built with a combination of technologies aimed at high performance and maintainability.

![Screenshot](/screenshot.png)

## Key Features:
- **WebSocket-Based Communication:** Real-time messaging between users using WebSocket technology for immediate data transfer.
- **Microservice Architecture:** Includes multiple services, such as:
  - `api-gateway`: PHP-based service (Laravel, Roadrunner) responsible for routing requests, managing JWT-based authentication, and communicating with other services.
  - `friends`: Go-based service that handles friend management and relational logic.
  - `chat`: Service for handling chat interactions.
  - `front`: React-based service that provides a responsive and interactive user interface.
  - `elk`: Centralized logging and monitoring.
  - `rabbitmq`: Messaging queue for handling asynchronous tasks between microservices.
  - `redis`: Used for caching to optimize performance by reducing frequent database queries.
- **Technology Stack:**
  - **Backend:** PHP 8.3 (Laravel 11, Roadrunner), Go
  - **Frontend:** React
  - **Databases:** PostgreSQL for `friends`, MySQL for `api-gateway` and `chat`, Redis for caching.
  - **Service-to-Service Communication:**
    - **Synchronous:** HTTP (REST) and gRPC for direct communication between services.
    - **Asynchronous:** RabbitMQ for message-based communication between microservices without requiring an immediate response.
- **Logging:** ELK Stack (Elasticsearch, Logstash, Kibana) for centralized logging and monitoring. Logs include a `trace_id` for request tracing, that allows to follow requests across different services for better tracking and debugging.
- **Dockerized Microservices:** All services are containerized with Docker, allowing for easy deployment and scaling.
- **Testing:** The project is covered with tests to ensure functionality and stability

---

### Installation and Setup

To run the project, follow these steps:

```bash
# clone the repository
git clone https://github.com/motyriev/messenger-pet-proj.git messenger

# create docker network
docker network create elk

# start rabbit
cd messenger/rabbitmq && docker-compose up --build -d

# start redis
cd ../redis && docker-compose up --build -d

# start ELK stack
cd ../elk && docker-compose up --build -d

# start friends service
cd ../friends && cp .env.example .env && docker-compose up --build -d

# start chat service
cd ../chat && cp .env.example .env && cp src/.env.example src/.env && docker-compose up --build -d

# start api gateway service
cd ../api-gateway && cp .env.example .env && cp src/.env.example src/.env && docker-compose up --build -d 

# start frontend service
cd ../front && cp .env.example .env && docker-compose up --build -d

# run test (optionally), migrations, seeds
cd ../ && ./setup.sh
```

Once all services are up and running, please wait for the front service image to build, and then you can access the application by navigating to the following url in your browser:
http://localhost:8000/login

app creds:  
http://localhost:8000/login  
user123@test.com  
123456

rabbitmq creds:  
http://localhost:15672/#/
guest  
guest

