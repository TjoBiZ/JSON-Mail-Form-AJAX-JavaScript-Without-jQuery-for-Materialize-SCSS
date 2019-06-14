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

let materializesforms = ["FormJSON", "AnotherFormJSON"];

materializesforms.forEach(function(pagesforms, materializesforms) {


    const contactForm = document.getElementById(pagesforms);

    contactForm.addEventListener('submit', function(event) { //отлавливаем событие нажатие на кнопку у формы
        event.preventDefault(); //отменяем все действия выполняемые по умолчанию браузером после этого события

        console.log('Действия пред отправкой JSON после нажатия'); // Тут можно задать действия сразу после нажатия кнопкп ПЕРЕД отправкаой JSON - "подождите сообщение отправляется."

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

        //заносим полученные данные из форм в переменные.
        let varcheck; //Временная переменная с полученными значениями из форм.
        let formName; ( varcheck = contactForm.querySelector('input[name="form_subject"]')) ? formName = varcheck.value : formName = false;
        let name; ( varcheck = contactForm.querySelector('input[name="name"]')) ? name = varcheck.value : name = false;
        let tel; ( varcheck = contactForm.querySelector('input[name="tel"]')) ? tel = varcheck.value : tel = false;
        let email; ( varcheck = contactForm.querySelector('input[name="email"]')) ? email = varcheck.value : email = false;
        let message; ( varcheck = contactForm.querySelector('textarea[name="message"]')) ? message = varcheck.value : message = false;
        let datepicker; ( varcheck = contactForm.querySelector('input[name="datepicker"]')) ? datepicker = varcheck.value : datepicker = false;
        let timepicker; ( varcheck = contactForm.querySelector('input[name="timepicker"]')) ? timepicker = varcheck.value : timepicker = false;
        let multipleoptions; ( varcheck = contactForm.querySelector(".multipleoptions .select-wrapper input")) ? multipleoptions = varcheck.value : multipleoptions = false;
        let radiochoice; if (contactForm.querySelector('input[type="radio"]')) {let groupnameradio = contactForm.querySelector('input[type="radio"]').name;
        let rates = document.getElementsByName(groupnameradio); // Только в document дереве, в переменной не работает уникализируем в html через атрибут name, который группирует объекты radio!
            for(var i = 0; i < rates.length; i++){
                if(rates[i].checked){
                    radiochoice = rates[i].id;
                }
            }
        } else {
            radiochoice = false;
        }
        let shopcheck; ( varcheck = contactForm.querySelector('input[name="shop"]')) ? shopcheck = varcheck.checked: shopcheck = false;
        if (shopcheck) { shopcheck = "yes" }
        let officecheck; (varcheck = contactForm.querySelector('input[name="office"]')) ? officecheck = varcheck.checked: officecheck = false;
        if (officecheck) { officecheck = "yes" }
        let partnercheck; ( varcheck = contactForm.querySelector('input[name="partner"]')) ? partnercheck = varcheck.checked: partnercheck = false;
        if (partnercheck) { partnercheck = "yes" }

        // Создаем ассоциативный массив - объект с полученными даннынми из переменных - форм
        let formData = { formName, name, tel, email, message, datepicker, timepicker, multipleoptions, radiochoice, shopcheck, officecheck, partnercheck, browser: navigator.userAgent, language: navigator.language, firstvititedsite: cookieValueFirstVisit, time: current_datetime.toString(), countpages: cookieValueCountPages };

        let data = JSON.stringify(formData); // Преобразуем данный массив в JSON Формат

        request.send(data);

    });


});
