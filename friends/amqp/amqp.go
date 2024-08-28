package amqp

import (
	"fmt"
	"github.com/streadway/amqp"
	"log"
)

func Init() (*amqp.Connection, *amqp.Channel, map[string]<-chan amqp.Delivery) {
	connection := initConnection()
	channel := initChannel(connection)

	queues := []string{"add_friend_request_queue", "manage_friend_request_queue"}
	msgs := make(map[string]<-chan amqp.Delivery)

	for _, queueName := range queues {
		q := declareQueue(channel, queueName)
		msgs[queueName] = consume(channel, q.Name)
	}

	return connection, channel, msgs
}

func initConnection() *amqp.Connection {
	connection, err := amqp.Dial("amqp://guest:guest@host.docker.internal:5672/")
	if err != nil {
		panic(fmt.Sprintf("Failed to establish connection to RabbitMQ: %v", err))
	}
	return connection
}

func initChannel(connection *amqp.Connection) *amqp.Channel {
	channel, err := connection.Channel()
	if err != nil {
		panic(fmt.Sprintf("Failed to open channel: %v", err))
	}
	return channel
}

func declareQueue(channel *amqp.Channel, queueName string) amqp.Queue {
	queue, err := channel.QueueDeclare(
		queueName, 
		true,  
		false,     
		false,  
		false,     
		nil,      
	)
	if err != nil {
		panic(fmt.Sprintf("Failed to declare queue: %v", err))
	}

	return queue
}

func consume(channel *amqp.Channel, queueName string) <-chan amqp.Delivery {
	msgs, err := channel.Consume(
		queueName, 
		"",   
		true, 
		false,
		false,  
		false,  
		nil,   
	)
	if err != nil {
		panic(fmt.Sprintf("Failed to register consumer: %v", err))
	}

	log.Printf("incoming msg: %s", msgs)

	return msgs
}
