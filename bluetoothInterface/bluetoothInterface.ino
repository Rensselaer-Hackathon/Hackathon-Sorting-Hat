#include <Serial.h>
#include <SoftwareSerial.h>

#define RxD 2
#define TxD 3

// bluetooth
SoftwareSerial blueToothSerial(RxD, TxD); 


void setup() {
  Serial.begin(9600);
  
  setupBlueToothConnection();
  
  Serial.println("hello, world");
  blueToothSerial.println("hello, world");
  
}

void loop() {
  
  if (blueToothSerial.available()) {
    // forward to UNO board
    Serial.write(blueToothSerial.read());
  }
  
  if (Serial.available()) {
    // send data to the bluetooth
    blueToothSerial.write(Serial.read());
  }
  
}

// The following code is necessary to setup the bluetooth shield
void setupBlueToothConnection() {
  blueToothSerial.begin(9600);// BluetoothBee BaudRate to default baud rate 38400
  blueToothSerial.print("\r\n+STWMOD=0\r\n"); //set the bluetooth work in slave mode
  blueToothSerial.print("\r\n+STNA=HC-05\r\n"); //set the bluetooth name as "SeeedBTSlave"
  blueToothSerial.print("\r\n+STOAUT=1\r\n"); // Permit Paired device to connect me
  blueToothSerial.print("\r\n+STAUTO=0\r\n"); // Auto-connection should be forbidden here
  delay(2000); // This delay is required.
  blueToothSerial.print("\r\n+INQ=1\r\n"); //make the slave bluetooth inquirable 
  Serial.println("The slave bluetooth is inquirable!");
  delay(2000); // This delay is required.
  blueToothSerial.flush();
}
