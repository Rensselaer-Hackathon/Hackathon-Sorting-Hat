import requests
import json
from time import sleep

try:
  import serial
except:
  print("ERROR initializing pySerial")

def makeAPICall(info):

  base_url = "http://vmutti.com/api.php?action=getIdea&APIKey="
  api_key = [x.strip() for x in open("api.key")][0]
  category = ""


  if (info == "1"):
    category = "&catName=web"
  elif (info == "2"):
    category = "&catName=hardware"
  elif (info == "3"):
    category = "&catName=mobile"
  elif (info == "4"):
    category = "&catName=desktop"
  elif (info == "5"):
    category = "$catName=design"

  url = base_url + api_key + category
  # make the call to the api
  response = requests.get(url)
  # check for any bad error codes
  if (response.status_code != 200):
    print("Status:", response.status_code)
    return

  data = response.json()
  print(data)
  data_list =  ["App Name: " + data["idea"]["title"], \
                "Category: " + data["idea"]["categoryName"], \
                "Description: " + data["idea"]["description"], \
                "Submitted by: " + data["idea"]["username"], \
                "Submitted on: " + data["idea"]["submittedDate"], \
                "AppId: " + data["idea"]["ideaId"]]
  return data_list


def connectBlueTooth(port):
  try:
    ser = serial.Serial(port, 9600, timeout=1)
  except:
    print("ERROR initializing com port")
    return 0
  print("com port initialized on:", port)
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
      data = makeAPICall(line)
      print(data)
      for d in data:
        sendBlueTooth(ser, d)
        sleep(2)
      # feed empty lines because
      for i in range(3):
        sendBlueTooth(ser, " ")
        sleep(1)
  else:
    print("serial ended")

# ------------------------------------------------------------------------------
if (__name__ == "__main__"):

  port = "COM5"
  ser = connectBlueTooth(port)

  if (ser):
    main(ser)
