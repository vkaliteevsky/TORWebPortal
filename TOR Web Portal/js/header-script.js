
function burgerMenu(selector) {
	let menu = $(selector);
	let button = menu.find('.burger-menu__button');
	let links = menu.find('.burger-menu__link');
	let overlay = menu.find('.burger-menu__overlay');

	button.on('click', (e) => {
		e.preventDefault();
		toggleMenu();
	});

	links.on('click', () => toggleMenu());
	overlay.on('click', () => toggleMenu());

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

var arr2 = '[{"order_id":"0","dt":"2019-03-08 14:18:45","user_id":"1","device_unq_id":"12","status_id":"1","priority_id":"1","text_request":"Не работает МФУ","user_id_engineer":null,"date_acceptance":"0000-00-00 00:00:00","date_executed":"0000-00-00 00:00:00","text_response":null,"device_info_id":"3","id":"1","short_name":"Short Name 3","long_name":"Long Name 3","short_descr":"Short Descr 3","long_descr":"Long Descr 3","vender":"Cannon","device_type_id":"102","descr":"Низкий"}]';
function init(){
	var obj = JSON.parse(arr);
	for (var key in obj){
		console.log(key);
		document.getElementById('goods-out').innerHTML +='<div class=" row dev__line"><div class="cart col-lg-6"><img src='+obj[key].img_src+'"../../c96232fw_d/public_html" alt=""><div class="name_item"><p class="name">'+obj[key].short_name+'</p></div></div><div class="cart1  col-lg-6"><a href="../orders/create-order.php"><button class="oformit">оформить заявку</button></a><input class="schetchik" type="text" placeholder="Счетчик"><button class="add-to-cart">Отправить показания</button></div></div>';
	}}
function init2(){
	var obj = JSON.parse(arr);
	for (var key in obj){
	  document.getElementById('Select').innerHTML +='<option>'+obj[key].short_name+'</option>';
	}}
function init3(){
	for (var key in obj2){
		document.getElementById('history').innerHTML +='<tr><td class="nomer"><p>'+obj2[key].order_id+'<p></td><td class="ystroistvo"><p>'+obj2[key].short_name+'<p></td><td class="status"><p>'+obj2[key].priority_id+'<p></td><td class="prioritet"><p>'+obj2[key].descr+'<p></td><td class="comment"><p>'+obj2[key].text_request+'<p></td></tr>';
	}
}

	