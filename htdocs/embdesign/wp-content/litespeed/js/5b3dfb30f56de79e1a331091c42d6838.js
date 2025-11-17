fetch("https://ipapi.co/json/").then(res=>res.json()).then(data=>{const city=data.city;if(city){document.cookie="user_city="+encodeURIComponent(city)+"; path=/;"}})
;