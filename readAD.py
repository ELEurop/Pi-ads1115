#!/usr/bin/python3
import smbus
import time
import sqlite3
import csv
import threading
import os
adc0 = 0x48
adc1 = 0x49
ch0  = 0xC
ch1  = 0xD
ch2  = 0xE
ch3  = 0xF
fsr0 = ((0x0),(0.0001875)) # 6,144V - 187,5000µV/bit
fsr1 = (0x2,0.000125) # 4,096V - 125,0000µV/bit
fsr2 = (0x4,0.0000625) # 2,048V -  62,5000µV/bit
fsr3 = (0x6,0.00003125) # 1,024V -  31,2500µV/bit
fsr4 = (0x8,0.000015625) # 0,512V -  15,6250µV/bit
fsr5 = (0xA,0.0000078125) # 0,256V -   7,8125µV/bit
dr0  = 0x8 # 128sps
dr1  = 0xA # 250sps
dr2  = 0xC # 475sps
dr3  = 0xE # 860sps
comp = 0x3 # off

class ADS1115:
	def __init__(self, bus=1, adr=0x49, comp=0x3):
		self.adr=adr  # Chipadresse
		self.bus= smbus.SMBus(bus)  # Busadresse
		self.comp=comp #Comperator default OFF
		self.setconf()
		datei = open("/var/www/html/readAD.log","a")
		datei.write("{} Start Adresse -> {}\n".format(time.asctime(time.localtime(time.time())),self.adr))
	def setadr(self, adr):
		self.adr=adr
	def setbus(self, bus):
		self.bus=smbus.SMBus(bus)
	def setcomp(self, comp):
		self.comp=comp
	def setconf(self, chn=0xc, fsr=(0x0,0.0001875), dr=0x8):
		self.chn=chn
		self.fsr=fsr
		self.dr=dr
		self.writeconf()
	def writeconf(self):
		# HiByte und LoByte des Configregisters erstellen
		confighbyte=self.chn << 4
		confighbyte= confighbyte | self.fsr[0]
		configlbyte=self.dr << 4
		configlbyte= configlbyte | self.comp
		configlbyte <<= 8
		config = configlbyte | confighbyte
		try:
			self.bus.write_word_data(self.adr, 1, config)
		except:
			return -1
		time.sleep(0.015) # Bereitstellungszeit lt. Datenblatt
		return 1
	def readwert(self):
		try:
			wert=self.bus.read_word_data(self.adr, 0)
		except:
			return "Fehler"
		wert=((wert<<8) & 0xff00) + (wert>>8) #HiByte und LoByte sind bei ARM-Architektur verkehrt (little endian)
		return (wert)
class chip:
	def __init__(self, bus=1, adr=0x49):
		dateirun = open("/var/www/html/ADRUN","w")
		dateirun.close()
		dateirun = open("/var/www/html/readAD.log","w")
		dateirun.close()
		try:
			datei = open("/var/www/html/messung.conf","r")
			csvdat = csv.reader(datei)
			self.adcs = []
			stopcount = 0
			for row in csvdat:
				if stopcount < 1:
					counter = 0;
					for dat in row:
						conn = sqlite3.connect("/var/www/html/ads1115_{}.db".format(counter))
						conn.execute('''CREATE TABLE IF NOT EXISTS WERTE
							(ID INTEGER PRIMARY KEY AUTOINCREMENT,
							 p1            REAL     NOT NULL,
							 p2            REAL     NOT NULL,
							 p3            REAL     NOT NULL,
							 p4            REAL     NOT NULL);''')
						conn.commit()
						conn.close()
						conf	= "/var/www/html/adc{}.conf".format(counter)
						db	= "/var/www/html/ads1115_{}.db".format(counter)
						self.adcs.append([ADS1115(1,int(dat)),conf,db,[(0,(0,0),0),(0,(0,0),0),(0,(0,0),0),(0,(0,0),0)]])
						counter = counter + 1
				stopcount = stopcount + 1
			datei.close()
		except:
			datei = open("/var/www/html/readAD.log","a")
			datei.write("{} Sie müssen die Konfiguration ausführen\n".format(time.asctime(time.localtime(time.time()))))
			os.remove("/var/www/html/ADRUN")
			datei.close()
			exit()

	def schreibeWerte(self, dbname, command, adr):
		db	= sqlite3.connect(dbname)
		db.execute(command)
		try:
			db.commit()
			db.close()
		except:
			datei = open("/var/www/html/readAD.log","a")
			datei.write("{} Datenbakfehler: Es fehlen zwei Sekunden in{}\n".format(time.asctime(time.localtime(time.time())),dbname))
			datei.close()
	def read(self):
		while True:
			anfang=time.time()
			adcscount = 0
			adressfehler = [0,0]
			for adc in self.adcs:
				try:
					datei = open(adc[1],"r")
					csvdat = csv.reader(datei)
					y = 0
					for row in csvdat:
						adr = 0
						fsrn1 = 0
						fsrn = 0
						drt = 0
						z = 0
						for dat in row:
							if (z == 0):
								adr=(int(dat))
							elif (z == 1):
								fsrn1 = int(dat)
							elif (z == 2):
								fsrn=(fsrn1,float(dat))
							elif (z == 3):
								drt=(int(dat))
								adc[3][y] = (adr,fsrn,drt)
							z = z + 1
						y = y + 1
					datei.close()
				except:
					os.remove("/var/www/html/ADRUN")
					datei = open("/var/www/html/readAD.log","a")
					datei.write("{} Lesefehler -> /var/www/html/adc{}.conf: Sie müssen die Konfiguration des Wandlers speichern\n".format(time.asctime(time.localtime(time.time())),adcscount))
					datei.close()
					exit()
				summen=[0,0,0,0]
				w=4
				sql="INSERT INTO WERTE(p1,p2,p3,p4)VALUES"
				while w>0:
					chindex=0
					for chn in adc[3]:
						adc[0].setconf(chn[0],chn[1],chn[2])
						mittelwert =0
						wert=0
						z=10
						while z>0:
							wert=adc[0].readwert()
							if (wert == "Fehler"):
								adressfehler[adcscount] = adressfehler[adcscount] +1
							else:
								mittelwert=mittelwert+wert*chn[1][1]
							z=z-1
						mittelwert=(mittelwert/10)
						summen[chindex]=summen[chindex]+mittelwert
						chindex=chindex+1
					w=w-1
				if (adressfehler[adcscount] > 20):
					os.remove("/var/www/html/ADRUN")
					datei = open("/var/www/html/readAD.log","a")
					datei.write("{} I2c-Adressfehler -> {}: Adresskonfiguration fehlerhaft. Der linke AD-Wandler muss eine gültige I2C-Adresse im Integerformat sein, bevor dem rechten eine zugewiesen wird\n".format(time.asctime(time.localtime(time.time())),adc[0].adr))
					datei.close()
				else :
					sql=sql+"({:2.3f},".format(summen[0]/4)
					sql=sql+"{:2.3f},".format(summen[1]/4)
					sql=sql+"{:2.3f},".format(summen[2]/4)
					sql=sql+"{:2.3f})".format(summen[3]/4)
					threading.Thread(target=self.schreibeWerte, args=(adc[2],sql,adc[0].adr)).start()
				adcscount =adcscount + 1
			zeit=(2-(time.time()-anfang))
#			print (zeit)
			if (zeit > 0):
				time.sleep(2-(time.time()-anfang))

wandler=chip(1,0x49)
wandler.read()

