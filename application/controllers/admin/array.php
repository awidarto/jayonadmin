stdClass Object ( 
	[assignment_date] => 2016-07-01 
	[delivery_type] => COD 
	[status] => delivered 
	[count] => 226 
	[box_count] => 1 
	[total_box_count] => 308 
	) 
stdClass Object ( 
	[assignment_date] => 2016-07-01 
	[delivery_type] => Delivery Only 
	[status] => delivered 
	[count] => 12 
	[box_count] => 1 
	[total_box_count] => 12 
	)

<?php 


stdClass Object ( 
	[assignment_date] => 2016-07-30 
	[delivery_type] => COD 
	[status] => delivered 
	[count] => 124 
	[total_box_count] => 144 (COD)
)

stdClass Object ( 
	[assignment_date] => 2016-07-30 
	[delivery_type] => Delivery Only  	
	[status] => delivered 
	[count] => 76 
	[total_box_count] => 107 (DO)
)


Array ( 
	[assignment_date] => 2016-07-30 	=> Hasil Dari> 
	[total_box_count] => 107 
	[delivered] => 76 
	[COD] => 124 
	[Delivery Only] => 76 
)

Bagai Mana Agar [total_box_count] adalah 
hasil dari [total_box_count](COD) + [total_box_count] (DO)???









?>