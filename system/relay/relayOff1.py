#!/usr/bin/python
import time
from simpleusbrelay import *

# initialize the library with the idVendor and Product id
idVendor=int("16c0", 16)
idProduct=int("05df", 16)
relaycontroller=simpleusbrelay(idVendor, idProduct)
#turn on relay 1
relaycontroller.array_off(1)
