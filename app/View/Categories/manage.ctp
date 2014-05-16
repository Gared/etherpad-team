<?php
    echo $this->Html->script('colpick');
    echo $this->Html->css('colpick');
?>
<script type="text/javascript">
var categoryMapping = {};

function initLoadCategories() {
	categoryMappingObj = $.parseJSON($("#PadCategoryCategoryMapping").val());
	console.log(categoryMappingObj);
	
	$.each(categoryMappingObj, function(padCategoryIndex, padCategory) {
		console.log(padCategoryIndex);
		$.each(padCategory, function(categoryIndex, category) {
			console.log(padCategoryIndex + ": " + category);
			addCategory(padCategoryIndex, category);
		});
	});
	/*if (!this.categories || (this.categories && this.categories.indexOf(categoryId) == -1)) {
		clonedCategory = ui.draggable.clone();
		clonedCategory.click(function() {
			this.parentNode.categories.splice(this.parentNode.categories.indexOf(this.id), 1);
			this.remove();
			categoryMapping[this.id] = this.categories;
			$("#PadCategoryCategoryMapping").val(JSON.stringify(categoryMapping));
		}).appendTo(this);
		if (!this.categories) {
			this.categories = [];
		}
		this.categories.push(categoryId);
		categoryMapping[this.id] = this.categories;
		$("#PadCategoryCategoryMapping").val(JSON.stringify(categoryMapping));
	}*/
}

function addCategory(padId, categoryId) {
	var padCategoryElem = document.getElementById(categoryId);
	var padElem = document.getElementById(padId);
	if (padCategoryElem && padElem) {
		var clonedElem = $(padCategoryElem).clone();
		var pad = $(padElem);
		clonedElem.click(function() {
			this.parentNode.categories.splice(this.parentNode.categories.indexOf(this.id), 1);
			this.remove();
			categoryMapping[this.id] = this.categories;
			$("#PadCategoryCategoryMapping").val(JSON.stringify(categoryMapping));
		}).appendTo(padElem);
		if (!padElem.categories) {
			padElem.categories = [];
		}
		padElem.categories.push(categoryId);
		categoryMapping[padElem.id] = padElem.categories;
		$("#PadCategoryCategoryMapping").val(JSON.stringify(categoryMapping));
	} else {
		console.log("Error: padCategoryElem: " + padCategoryElem + ", padElem: " + padElem);
	}
}

$(function() {
	$('.color-box').colpick({
		colorScheme:'dark',
		layout:'rgbhex',
		color:'ff8800',
		onSubmit:function(hsb,hex,rgb,el) {
			$(el).css('background-color', '#'+hex);
			$(el).colpickHide();
	        $('#CategoryColor').val(hex);
		}
	})
	.css('background-color', '#ff8800');

	$(".draggable").draggable({
		appendTo: "body",
		helper: "clone"
	});
	$(".droppable").droppable({
		drop: function( event, ui ) {
			if (ui.draggable.get(0) && ui.draggable.get(0).id) {
				categoryId = ui.draggable.get(0).id;
				if (!this.categories || (this.categories && this.categories.indexOf(categoryId) == -1)) {
					clonedCategory = ui.draggable.clone();
					clonedCategory.click(function() {
						this.parentNode.categories.splice(this.parentNode.categories.indexOf(this.id), 1);
						this.remove();
						categoryMapping[this.id] = this.categories;
						$("#PadCategoryCategoryMapping").val(JSON.stringify(categoryMapping));
					}).appendTo(this);
					if (!this.categories) {
						this.categories = [];
					}
					this.categories.push(categoryId);
					categoryMapping[this.id] = this.categories;
					$("#PadCategoryCategoryMapping").val(JSON.stringify(categoryMapping));
				}
			}
		}
	});
	initLoadCategories();
});
</script>
<div>
    <?php
    echo $this->Form->create('Category', array('controller' => 'category', 'action' => 'create'));
    echo '<table><tr><td>';
    echo $this->Form->input('name');
    echo '</td><td>';
    echo $this->Form->hidden('color', array('value' => 'ff8800'));
    echo '<div class="color-box"></div></td></tr></table>';
    echo $this->Form->hidden('mappingId', array('value' => $mappingId));

    echo $this->Form->end(__('Create category'));
    ?>
    <h2><? echo __('Categories'); ?></h2>
    <table>
    	<tr>
    		<td>
    			<table>
    				<?php foreach ($pads as $pad): ?>
    					<tr>
    						<td>
    							<div id="<?php echo $pad->groupName."$".$pad->padName ?>" class="droppable padname">
    								<?php echo $pad->padName; ?>
    								<?php
    									if (1==2 && array_key_exists($pad->groupName."$".$pad->padName, $padcategories)) {
    									 	foreach ($padcategories[$pad->groupName."$".$pad->padName] as $padcategory): ?>
    											<span id="<?php echo $padcategory['Category']['id']; ?>" class="category draggable" style="background-color:#<?php echo $padcategory['Category']['color']; ?>;">
						                    	<?php echo $padcategory['Category']['name']; ?>
						                	</span>
    								<?php
    										endforeach;
    									} else {
    										//echo "key: ".$pad->padName;
    									}
    								 ?>
    							</div>
    						</td>
    						<td>

    						</td>
    					</tr>
    				<?php endforeach; ?>
    			</table>
    		</td>
    		<td>
			    <table cellpadding="5px" class="categoryTable">
			        <?php foreach ($categories as $category): ?>
			        <tr>
			            <td>
			                <?php
			                echo $this->Html->link($this->Html->image("delete.png", array("alt" => "(Delete)", "title" => __("Delete category"), "width" => "20px", "height" => "20px")),
			                    array('controller' => 'categories', 'action' => 'delete', $mappingId, $category['Category']['id']),
			                    array('escape' => false),
			                    __("Do you really want to delete this category?"));
			                ?>
			            </td>
			            <td>
			                <span id="<?php echo $category['Category']['id']; ?>" class="category draggable" style="background-color:#<?php echo $category['Category']['color']; ?>;">
			                    <?php echo $category['Category']['name']; ?>
			                </span>
			            </td>
			            <td>
			                <?php echo "#".$category['Category']['color']; ?>
			            </td>
			        </tr>
			        <?php endforeach; ?>
			    </table>
			</td>
		</tr>
	</table>
	<?php
		echo $this->Form->create('PadCategory', array('controller' => 'category', 'action' => 'saveMapping/'.$mappingId));
	    echo $this->Form->hidden('categoryMapping', array('value' => $categoryMapping));
	    echo $this->Form->end(__('Save'));
	?>
</div>