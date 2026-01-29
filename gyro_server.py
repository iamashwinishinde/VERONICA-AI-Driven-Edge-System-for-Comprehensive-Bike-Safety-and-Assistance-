import asyncio
import websockets
import json
import serial
import serial.tools.list_ports

# Configuration
WEBSOCKET_PORT = 8080
SERIAL_BAUDRATE = 115200

class GyroServer:
    def __init__(self):
        self.clients = set()
        self.serial_port = None
        
    async def register(self, websocket):
        self.clients.add(websocket)
        print(f"Client connected. Total clients: {len(self.clients)}")
        
    async def unregister(self, websocket):
        self.clients.remove(websocket)
        print(f"Client disconnected. Total clients: {len(self.clients)}")
        
    def find_esp32_port(self):
        """Find the ESP32 serial port"""
        ports = list(serial.tools.list_ports.comports())
        for port in ports:
            if "CP210" in port.description or "CH340" in port.description:  # Common ESP32 USB-to-Serial chips
                return port.device
        return None
        
    async def connect_serial(self):
        """Connect to ESP32 via serial"""
        esp32_port = self.find_esp32_port()
        if not esp32_port:
            print("ESP32 not found. Please connect the device.")
            return False
            
        try:
            self.serial_port = serial.Serial(esp32_port, SERIAL_BAUDRATE)
            print(f"Connected to ESP32 on {esp32_port}")
            return True
        except Exception as e:
            print(f"Error connecting to ESP32: {e}")
            return False
            
    async def handle_serial_data(self):
        """Read and process data from ESP32"""
        while True:
            if self.serial_port and self.serial_port.in_waiting:
                try:
                    line = self.serial_port.readline().decode('utf-8').strip()
                    data = json.loads(line)
                    
                    # Broadcast data to all connected web clients
                    if self.clients:
                        await asyncio.gather(
                            *[client.send(json.dumps(data)) for client in self.clients]
                        )
                except Exception as e:
                    print(f"Error processing serial data: {e}")
                    
            await asyncio.sleep(0.01)  # Small delay to prevent CPU overload
            
    async def handler(self, websocket):
        """Handle WebSocket connections"""
        await self.register(websocket)
        try:
            async for message in websocket:
                # Handle any messages from web clients if needed
                pass
        finally:
            await self.unregister(websocket)
            
    async def main(self):
        """Main server routine"""
        # Connect to ESP32
        if not await self.connect_serial():
            print("Failed to connect to ESP32. Server will start but won't receive data.")
            
        # Start WebSocket server
        async with websockets.serve(self.handler, "localhost", WEBSOCKET_PORT):
            print(f"WebSocket server started on ws://localhost:{WEBSOCKET_PORT}")
            
            # Start serial data handling
            await self.handle_serial_data()
            
if __name__ == "__main__":
    server = GyroServer()
    asyncio.run(server.main()) 