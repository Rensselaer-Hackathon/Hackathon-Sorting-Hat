import requests
import shelve
import json
from random import shuffle
from time import sleep

try:
	import serial
except:
	print("ERROR initializing pySerial")

def loadData(shelf, file_name):
	for line in open(file_name):
		line = line.strip().split(',')
		date = line[0].strip()
		descrip = line[1].strip()
		print(date, " -- ", descrip)
		if date not in shelf:
			shelf[date] = {
				'text': descrip,
				'count': 0
			}

def getRandomIdea(category_num):
	data = shelve.open("ideaDB")
	k = list(data.keys())
	shuffle(k)
	for d in k:
		if (data[d]['count'] <= repeat):
			data[d]['count'] += 1
			return [d, data[d]['text']]

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
			data = getRandomIdea(line)
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

	file_name = "idea_list.csv"
	data = shelve.open("ideaDB")
	loadData(data, file_name)
	repeat = 0
	port = "COM4"
	ser = connectBlueTooth(port)

	if (ser):
		main(ser)
