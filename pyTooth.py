import requests
import json

try:
  import serial
except:
  print("ERROR initializing pySerial")

def makeAPICall(info):

  base_url = "http://vmutti.com/api.php?action=getIdea&APIKey="
  api_key = [x.strip() for x in open("api.key")][0]
  url = base_url + api_key
  # make the call to the api
  response = requests.get(url)
  # check for any bad error codes
  if (response.status_code != 200):
    print("Status:", response.status_code)
    return

  data = response.json()
  for k,v in data["idea"].items():
    print(k,v)

  if (info == "1"):
    pass

def connectBlueTooth(port):
  try:
    ser = serial.Serial(port, 9600, timeout=1)
  except:
    print("ERROR initializing com port")
    return 0
  return ser

def sendBlueTooth(ser, msg):
  msg = msg.encode("utf-8")
  ser.write(msg)
  ser.flush()

def main(ser):
  while (ser.isOpen()):
    # read whatever is in the buffer
    line = ser.readline()
    # check to make sure it's not garbage
    if (len(line) > 0):
      # decode the bytes to a string
      line = line.decode("utf-8")
      # remove the crap
      # line = line.replace("'", "").replace("\\n", "").replace("\\r", "").strip()
      print(line)
      # makeAPICall(line)
      sendBlueTooth(ser, "alright alright alright")
  else:
    print("serial ended")

# ------------------------------------------------------------------------------
if (__name__ == "__main__"):

  port = "COM6"
  ser = connectBlueTooth(port)

  if (ser):
    main(ser)
