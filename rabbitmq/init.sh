#!/bin/bash

sleep 10

rabbitmqadmin declare exchange name=message_notify_exchange type=direct durable=true
rabbitmqadmin declare exchange name=message_store_exchange type=direct durable=true

rabbitmqadmin declare queue name=message_notify_queue durable=true
rabbitmqadmin declare queue name=message_store_queue durable=true
rabbitmqadmin declare queue name=add_friend_request_queue durable=true
rabbitmqadmin declare queue name=manage_friend_request_queue durable=true

rabbitmqadmin declare binding source=message_notify_exchange destination=message_notify_queue
rabbitmqadmin declare binding source=message_store_exchange destination=message_store_queue
rabbitmqadmin declare binding source=amq.default destination=add_friend_request_queue
rabbitmqadmin declare binding source=amq.default destination=manage_friend_request_queue
