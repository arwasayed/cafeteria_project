<?php include_once 'header2.php';?>
<h1 class="head">All Products</h1>
<div><a class="nav-link"  href="#">Add User</a></div>
<div class="containerr">
	
	<div class="table">
		<div class="table-header">
			<div class="header__item"><a id="Product" class="filter__link" href="#">Product</a></div>
			<div class="header__item"><a id="Price" class="filter__link filter__link--number" href="#">Price</a></div>
			<div class="header__item"><a id="Image" class="filter__link filter__link--number" href="#">image</a></div>
			<div class="header__item"><a id="Active" class="filter__link filter__link--number" href="#">Active</a></div>
		</div>
		<div class="table-content">	
			<div class="table-row">		
				<div class="table-data">Tom</div>
				<div class="table-data">2</div>
				<div class="table-data">0</div>
				<div class="table-data">1</div>
			</div>
			<div class="table-row">
				<div class="table-data">Dick</div>
				<div class="table-data">1</div>
				<div class="table-data">1</div>
				<div class="table-data">2</div>
			</div>
			<div class="table-row">
				<div class="table-data">Harry</div>
				<div class="table-data">0</div>
				<div class="table-data">2</div>
				<div class="table-data">2</div>
			</div>
            <div class="table-row">
				<div class="table-data">Harry</div>
				<div class="table-data">0</div>
				<div class="table-data">2</div>
				<div class="table-data">2</div>
			</div><div class="table-row">
				<div class="table-data">Harry</div>
				<div class="table-data">0</div>
				<div class="table-data">2</div>
				<div class="table-data">2</div>
			</div><div class="table-row">
				<div class="table-data">Harry</div>
				<div class="table-data">0</div>
				<div class="table-data">2</div>
				<div class="table-data">2</div>
			</div><div class="table-row">
				<div class="table-data">Harry</div>
				<div class="table-data">0</div>
				<div class="table-data">2</div>
				<div class="table-data">2</div>
			</div><div class="table-row">
				<div class="table-data">Harry</div>
				<div class="table-data">0</div>
				<div class="table-data">2</div>
				<div class="table-data">2</div>
			</div><div class="table-row">
				<div class="table-data">Harry</div>
				<div class="table-data">0</div>
				<div class="table-data">2</div>
				<div class="table-data">2</div>
			</div>
		</div>	
	</div>
</div>
<!-- <script>

var properties = [
	'name',
	'wins',
	'draws',
	'losses',
	'total',
];

$.each( properties, function( i, val ) {
	
	var orderClass = '';

	$("#" + val).click(function(e){
		e.preventDefault();
		$('.filter__link.filter__link--active').not(this).removeClass('filter__link--active');
  		$(this).toggleClass('filter__link--active');
   		$('.filter__link').removeClass('asc desc');

   		if(orderClass == 'desc' || orderClass == '') {
    			$(this).addClass('asc');
    			orderClass = 'asc';
       	} else {
       		$(this).addClass('desc');
       		orderClass = 'desc';
       	}

		var parent = $(this).closest('.header__item');
    		var index = $(".header__item").index(parent);
		var $table = $('.table-content');
		var rows = $table.find('.table-row').get();
		var isSelected = $(this).hasClass('filter__link--active');
		var isNumber = $(this).hasClass('filter__link--number');
			
		rows.sort(function(a, b){

			var x = $(a).find('.table-data').eq(index).text();
    			var y = $(b).find('.table-data').eq(index).text();
				
			if(isNumber == true) {
    					
				if(isSelected) {
					return x - y;
				} else {
					return y - x;
				}

			} else {
			
				if(isSelected) {		
					if(x < y) return -1;
					if(x > y) return 1;
					return 0;
				} else {
					if(x > y) return -1;
					if(x < y) return 1;
					return 0;
				}
			}
    		});

		$.each(rows, function(index,row) {
			$table.append(row);
		});

		return false;
	});

});
</script> -->

<?php include_once 'footer.php';?>
