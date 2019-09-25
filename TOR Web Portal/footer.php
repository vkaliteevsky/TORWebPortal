</div>
<div class="col-1 col-sm-0"></div>
</div>
<script>
    try {
        var el = document.getElementById('main__menu').getElementsByTagName('a');
        var url = document.location.href;
        for (var i = 0; i < el.length; i++) {
            if (url == el[i].href) {
                el[i].className += ' act';
            }
            ;
        }
        ;
    } catch (e) {
    }

    function burgerMenu(selector) {
        let menu = $(selector);
        let button = menu.find('.burger-menu__button');
        let links = menu.find('.burger-menu__link');
        let overlay = menu.find('.burger-menu__overlay');

        button.on('click', function(e) {
            e.preventDefault();
            toggleMenu();
        });

        links.on('click', function () { toggleMenu(); });
        overlay.on('click', function () { toggleMenu(); });

        function toggleMenu() {
            menu.toggleClass('burger-menu__active');

            if(menu.hasClass('burger-menu__active')) {
                $('body').css('overflow', 'visible');
            } else {
                $('body').css('overflow', 'visible');
            }
        }
    }

    burgerMenu('.burger-menu');

    // --------------------------------------------//

    //var arr2 = '[{"order_id":"0","dt":"2019-03-08 14:18:45","user_id":"1","device_unq_id":"12","status_id":"1","priority_id":"1","text_request":"Не работает МФУ","user_id_engineer":null,"date_acceptance":"0000-00-00 00:00:00","date_executed":"0000-00-00 00:00:00","text_response":null,"device_info_id":"3","id":"1","short_name":"Short Name 3","long_name":"Long Name 3","short_descr":"Short Descr 3","long_descr":"Long Descr 3","vender":"Cannon","device_type_id":"102","descr":"Низкий"}]';

</script>
<!-- <script src="/js/header-script.js" defer></script>
<script src="/js/jquery-3.3.1.min.js" defer></script> -->
</body>
</html>