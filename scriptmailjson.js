let cookieValueFirstVisit = document.cookie.replace(/(?:(?:^|.*;\s*)firstvisited\s*\=\s*([^;]*).*$)|^.*$/, "$1"); //Парсим есть ли данная запись в cookie - на первый визит сайта.
//let cookieValueCountPages; // Объявляем переменную, чтобы зайти в область видимости
    if(!cookieValueFirstVisit) { //Если нету, то создаем cookie со временем о первом визите и запускаем-создаем счетчик просмотренных страниц.
        let timenow = new Date();
        let firsttime ='firstvisited=' + timenow.toString();
        document.cookie = firsttime + "; domain=." + document.domain + "; path=/; expires=Thu, 01 Jan 2030 00:00:00 UTC;";
        document.cookie = "countpages=1 ; domain=." + document.domain + "; path=/; expires=Thu, 01 Jan 2030 00:00:00 UTC;";
    } else { //Увеличиваем счетчик на единиц
			  window.cookieValueCountPages = document.cookie.replace(/(?:(?:^|.*;\s*)countpages\s*\=\s*([^;]*).*$)|^.*$/, "$1");
			  document.cookie = "countpages=" + ++window.cookieValueCountPages + "; domain=." + document.domain + "; path=/; expires=Thu, 01 Jan 2030 00:00:00 UTC;";
    }

const contactForm = document.getElementById("FormJSON");

contactForm.addEventListener('submit', function(event) { //отлавливаем событие нажатие на кнопку у формы
    event.preventDefault(); //отменяем все действия выполняемые по умолчанию браузером после этого события

    let request = new XMLHttpRequest();
    let url = "plugins/mail/jsonmailformjsajax.php";
    request.open("POST", url, true);
    request.setRequestHeader("Content-Type", "application/json");
    request.onreadystatechange = function () {
        if (request.readyState === 4 && request.status === 200) { // сценарий скриптов после ответа от сервера.
            let jsonData = JSON.parse(request.response);
            console.log(jsonData);
            console.log(request.response);
            contactForm.innerHTML = "<h3>Спасибо за заявку, " + jsonData['name'] + '!</h3><br> Ваше сообщение: <em style="color:#516eee">' + jsonData['message'] + '</em> отправлено. Ждите ответа, скоро с Вами свяжуться';
        }
    };

    let current_datetime = new Date(); // Время и часовой пояс на компьютере клиента

    //заносим значения булиан и других форм в переменные
    let shopcheck = contactForm.querySelector('input[name="shop"]').checked;
    if (shopcheck) { shopcheck = "yes" }
    let officecheck = contactForm.querySelector('input[name="office"]').checked;
    if (officecheck) { officecheck = "yes" }
    let partnercheck = contactForm.querySelector('input[name="partner"]').checked;
    if (partnercheck) { partnercheck = "yes" }
    let rates = document.getElementsByName('group1');
    let radiochoice;
    for(var i = 0; i < rates.length; i++){
        if(rates[i].checked){
            radiochoice = rates[i].id;
        }
    }
    let multipleoptions = contactForm.querySelector(".multipleoptions .select-wrapper input").value;

    // Создаем ассоциативный массив - объект с полученными данынми из форм
    let formData = {
        formName: contactForm.querySelector('input[name="form_subject"]').value,
        name: contactForm.querySelector('input[name="name"]').value,
        tel: contactForm.querySelector('input[name="tel"]').value,
        email: contactForm.querySelector('input[name="email"]').value,
        message: contactForm.querySelector('textarea[name="message"]').value,
        datepicker: contactForm.querySelector('input[name="datepicker"]').value,
        timepicker: contactForm.querySelector('input[name="timepicker"]').value,
        radiochoice,
        multipleoptions,
        shopcheck,
        officecheck,
        partnercheck,
        browser: navigator.userAgent,
        language: navigator.language,
        firstvititedsite: cookieValueFirstVisit,
        time: current_datetime.toString(),
        countpages: cookieValueCountPages
    };

    let data = JSON.stringify(formData); // Преобразуем данный массив в JSON Формат

    request.send(data);

});