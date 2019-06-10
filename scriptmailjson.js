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

    // Создаем ассоциативный массив - объект с полученными данынми из форм
    let formData = {
        formName: document.querySelector('input[name="form_subject"]').value,
        name: document.querySelector('input[name="name"]').value,
        tel: document.querySelector('input[name="tel"]').value,
        email: document.querySelector('input[name="email"]').value,
        message: document.querySelector('textarea[name="message"]').value,
        browser: navigator.userAgent,
        language: navigator.language,
        time: current_datetime.toString()
    };

    let data = JSON.stringify(formData); // Преобразуем данный массив в JSON Формат

    request.send(data);

});