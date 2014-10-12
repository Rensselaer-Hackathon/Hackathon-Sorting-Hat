#include <Keypad.h>
#include <Adafruit_Thermal.h>
#include <avr/pgmspace.h>
#include <SoftwareSerial.h>
#include "pitches.h"

#define RxD 0
#define TxD 1

const byte ROWS = 4; // Four rows
const byte COLS = 3; // Three columns
// Define the Keymap
char keys[ROWS][COLS] = {
  {'1','2','3'},
  {'4','5','6'},
  {'7','8','9'},
  {'*','0','#'}
};
// Connect keypad ROW0, ROW1, ROW2 and ROW3 to these Arduino pins.
byte rowPins[ROWS] = { 5, 6, 7, 8 };
// Connect keypad COL0, COL1 and COL2 to these Arduino pins.
byte colPins[COLS] = { 2, 3, 4 };
// Create the Keypad
Keypad kpd = Keypad( makeKeymap(keys), rowPins, colPins, ROWS, COLS );

// speaker pin
int speakerPin = 9;
// sample melody
int melody[] = {NOTE_B3, NOTE_E4, NOTE_G4, NOTE_FS4, NOTE_E4, NOTE_B4, NOTE_A4, NOTE_FS4,
                NOTE_G4, NOTE_FS4, NOTE_E4, NOTE_DS3, NOTE_F4, NOTE_C3, NOTE_G3, NOTE_C3, NOTE_C3,
                NOTE_E3, NOTE_G4, NOTE_F4, NOTE_E4, NOTE_B4, NOTE_D4, NOTE_D4, NOTE_C4, NOTE_A3};
// note durations: 4 = quarter note, 8 = eighth note, etc.:
int noteDurations[] = {4, 3, 8, 4, 2, 4, 1.5, 1.5,
                       4, 8, 4, 2, 4, 2, 4, 2, 4,
                       4, 8, 4, 3, 4, 2, 4, 2, 4};
                       
// bluetooth stuff
//SoftwareSerial BlueTooth(RxD, TxD); 

// Thermal printer stuff
int printer_RX_Pin = 10;  // This is the green wire
int printer_TX_Pin = 11;  // This is the yellow wire

Adafruit_Thermal printer(printer_RX_Pin, printer_TX_Pin);

String line = "";

void playHarryPotter() {
  // iterate over the notes of the melody:
  for (int thisNote = 0; thisNote < 26; thisNote++) {

    // to calculate the note duration, take one second
    // divided by the note type.
    //e.g. quarter note = 1000 / 4, eighth note = 1000/8, etc.
    int noteDuration = 1000/noteDurations[thisNote];
    tone(speakerPin, melody[thisNote],noteDuration);

    // to distinguish the notes, set a minimum time between them.
    // the note's duration + 30% seems to work well:
    int pauseBetweenNotes = noteDuration * 1.30;
    delay(pauseBetweenNotes);
    // stop the tone playing:
    noTone(speakerPin);
  }
  
}

void setup() {
  Serial.begin(9600);

  //BlueTooth.begin(9600); // initialize connection with the Arduino Pro Mini with the Bluetooth dongle 
  
  printer.begin(9600);
  
}

void loop() {
  char key = kpd.getKey();
  if (key) {

    //BlueTooth.print(key);
    
    if (key == '*') {
      playHarryPotter();
      printer.println("HARRY POTTER!");
      printer.println();
    }
    else {
      Serial.print(key);
    }
    
  }
  
  //if (BlueTooth.available()) {
  if (Serial.available()) {
    delay(50);
    while (Serial.available()) {
      char c = (char)Serial.read();
      line = line + c;
    }
    printer.println(line);
    line = "";
    /*
    char c = (char)Serial.read();
    if (c == '~') {
      printer.println(line);
      line = "";
    }
    else {
      line = line + c;
    }
    */
  }
  
}
