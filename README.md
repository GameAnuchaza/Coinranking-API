# Coinranking API
เป็นระบบเกี่ยวกับ Coinranking ซึ่งได้มาจากการดึง Api จากเว็บ https://coinranking.com/ แบบฟรีๆ โค้ดนี้สามารถกดดูระเอียดของเหรียญได้ สามารถค้นหาเหรียญได้

# วิธีเอา token จากเว็บ Coinranking เพื่อเรียกใช้ API 

1. ไปที่เว็บ https://developers.coinranking.com/api
2. สมัครเว็บไซต์ และเข้าสู่ระบบ
3. เอา token มาใส่ในโค้ดที่มีคำว่า //token
4. ทำการทดสอบ แล้วนำไปออกแบบเพิ่มเติม

# กรณีที่ดึงข้อมูลไม่ได้ให้ดาวโหลดไฟล์ที่ https://curl.se/docs/caextract.html ชื่อไฟล์ cacert.pem เอาไปใส่ต่อจากtoken
เช่น CURLOPT_HTTPHEADER => array(
    "x-access-token: //token"
    ),
    CURLOPT_CAINFO => 'cacert.pem', //ที่เก็บไฟล์ที่ใหนส่วนผมเก็บไว้ในที่เดียวกัน


# สามารถดูเพิ่มเติมเกี่ยวกับโค้ดได้[ที่นี่](https://developers.coinranking.com/api/documentation)?
