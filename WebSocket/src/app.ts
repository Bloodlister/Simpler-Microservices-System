import WebSocket from "./WebSocket";
import RabbitMq from "./RabbitMq";

const websocket = new WebSocket();
const rabbitMq = new RabbitMq();
rabbitMq.start();

websocket.start();
