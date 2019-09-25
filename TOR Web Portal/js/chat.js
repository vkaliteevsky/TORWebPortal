var textarea = document.querySelector('textarea');

textarea.addEventListener('keydown', autosize);

function autosize() {
    var el = this;
    setTimeout(function() {
        el.style.cssText = 'height:auto; padding:0';
        el.style.cssText = '-moz-box-sizing:content-box';
        el.style.cssText = 'height:' + el.scrollHeight + 'px';
    }, 0);
}

function addMessageHTML(user, dt, text) {
    const html =
        "<div class=\"chatList__item\">\n" +
            "<div class=\"chatList__itemText\">" + text + "</div>\n" +
            "<div class=\"chatList__itemInfo\">\n" +
                "<div class=\"chatList__itemInfoAccent\">" + user + "</div>\n" +
                dt + "\n" +
            "</div>\n" +
        "</div>\n";
    $(".chatList").append(html);
}

function updateChat() {
    const orderId = $('#orderId').val();
    $.ajax({
        url: "../php/handleGetChatMessages.php",
        type: "post",
        data: {
            orderId: orderId
        },
        success: function(messagesStr) {
            const messagesObj = JSON.parse(messagesStr);
            for (let i = 0; i < messagesObj.length; i++) {
                const msgItem = messagesObj[i];
                addMessageHTML(msgItem['user_short_name'], msgItem['dt'], msgItem['text']);
            }
        }
    });
}

function sendChatMessage() {
    const messageText = $('#newMessage').val();
    const orderId = $('#orderId').val();
    $.ajax({
        url: "../php/handleSendMessage.php",
        type: "post",
        data: {
            orderId: orderId,
            messageText: messageText
        },
        success: function(addedMessageStr) {
            // alert(addedMessageStr);
            const messagesObj = JSON.parse(addedMessageStr);
            const n = messagesObj.length;
            if (n != 1) {
                alert("Server error while adding new chat message");
            } else {
                const user = messagesObj[0]['user_short_name'];
                const dt = messagesObj[0]['dt'];
                const text = messagesObj[0]['text'];
                addMessageHTML(user, dt, text);
                $("#newMessage").val("");
                $('#chatListId').scrollTop($('#chatListId')[0].scrollHeight);
            }
        }
    });
}

$('#newMessage').keydown(function (e) {
    if (e.ctrlKey && e.keyCode == 13) {
        sendChatMessage();
    }
});

$('#chatListId').scrollTop($('#chatListId')[0].scrollHeight);