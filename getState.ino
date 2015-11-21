/*
WiFiGlcdTempController - getstate.ino

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

This software uses multiple libraries that are subject to additional
licenses as defined by the author of that software. It is the user's
and developer's responsibility to determine and adhere to any additional
requirements that may arise.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

void getState(uint8_t x)
{
uint16_t chipCRCval, chipBufferCRC;
  ds.reset();
  ds.select(ds2406[x].switchAddr);
  ds.write(ds2406MemRd);
  ds2406[x].switchData[0] = ds2406MemRd;
  ds.write(0x0); //2406 Addr Low
  ds2406[x].switchData[1] = 0;
  ds.write(0x0); //2406 Addr Hgh
  ds2406[x].switchData[2] = 0;
  for(int i = 3; i <  switchDataSize; i++)
  {
    ds2406[x].switchData[i] = ds.read();
  }
  ds.reset();

  chipCRCval = ~(ds.crc16(ds2406[x].switchData, 11)) & 0xFFFF;
  chipBufferCRC = ((ds2406[x].switchData[12] << 8) | ds2406[x].switchData[11]);
  
  if(setDebug & switchDebug)
  {
    Serial.print("ds2406[");
    Serial.print(x);
    Serial.print("].switchData = ");
    for(int i = 0; i <  switchDataSize; i++)
    {
      if(ds2406[x].switchData[i] < 0x0f)
      {
        Serial.print("0x0");
      }else{
        Serial.print("0x");
      }
      Serial.print(ds2406[x].switchData[i], HEX);
      Serial.print(", ");    
    }
    Serial.println();
    Serial.print("chipCRCval = 0x");
    Serial.print(chipCRCval, HEX);
    Serial.print(", chipBufferCRC = 0x");
    Serial.println(chipBufferCRC, HEX);
  }

  if(chipCRCval == chipBufferCRC)
  {
    if(ds2406[x].switchData[10] & dsPIO_A)
    {
      ds2406[x].switchStatus = switchStatusOFF;
    }else{
      ds2406[x].switchStatus = switchStatusON;
    }
  }else{
    if(setDebug & switchDebug)
    {
      Serial.print("Switch[");
      Serial.print(x);
      Serial.println("] CRC fail - Trying again");
    }
  }

  if(setDebug & switchDebug)
  {
    Serial.print(", ds2406[");
    Serial.print(x);
    Serial.print("].switchStatus = ");
    Serial.println( (char) ds2406[x].switchStatus );
  }
}

