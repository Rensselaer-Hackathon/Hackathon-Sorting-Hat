#include <Keypad.h>
#include <SoftwareSerial.h>
#include "pitches.h"

#define RxD 11
#define TxD 10

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
SoftwareSerial BlueTooth(RxD, TxD); 

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

  BlueTooth.begin(9600); // initialize connection with the Arduino Pro Mini with the Bluetooth dongle 
}

void loop() {
  char key = kpd.getKey();
  if (key) {
    Serial.println(key);
    
    BlueTooth.println("hello");
    
    if (key == '*') {
      playHarryPotter();
    }
    
  }
}
