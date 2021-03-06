<?php //options - An array of the options the user selected for this block
/*
This is the PHP ternary operator (also known as a conditional operator)
- if first operand evaluates true --> evaluate as second operand,
else evaluate as third operand.
*/
$position = isset($options['file-position'])  //if user decided 'file-position'
    ? html_escape($options['file-position'])  //set $position to that
    : 'left';                                 //else: set $position to the default ('left')
$size = isset($options['file-size'])
    ? html_escape($options['file-size'])
    : 'fullsize';
$captionPosition = isset($options['captions-position'])
    ? html_escape($options['captions-position'])
    : 'center';
?>

<?php
/*
fetchObject($sql, $params = array())
Retrieve a single record object from the database.
Parameters: $sql (string)
Returns: Omeka_Record_AbstractRecord or null if no record
*/

// Database Querying Content:
$db = get_db(); //Creates an object to query  Omeka_Db
$table = $db->getTable('ExhibitPage'); //Gets the table
// For this test, I had it query my exhibit with ID 1
$pageOne = $table->fetchObject('SELECT * FROM omeka_exhibit_pages WHERE id = 1');
echo $pageOne->getProperty('title');

/* Retrieves the table for Exhibit page block and creates an array to
hold all of the page blocks*/
$blocksTable = $db->getTable('ExhibitPageBlock');
$pageBlocks = $blocksTable->fetchObjects('SELECT * FROM omeka_exhibit_page_blocks WHERE page_id = 1');

/*gets the table for all the ExhibitBlockAttachemnts and fetches arrays
of all of them that exist on the page where 'id' = 1. It does this by
identifying them based on the exhibit page blocks*/
$attachmentTable = $db->getTable('ExhibitBlockAttachment');
foreach ($pageBlocks as $block):
  $pageBlockAttachments = $attachmentTable->fetchObjects('SELECT * FROM omeka_exhibit_block_attachments WHERE block_id ='.$block->getProperty('id'));
endforeach; //MINE!!
// End of Database Querying Content
?>

<div class="slider-pro <?php echo $position; ?> <?php echo $size; ?> captions-<?php echo $captionPosition; ?>">
    <div class="sp-slides">
      <?php /*attachments - An array of the attachment objects for items the user has attached to this block
          To display the attached items and files in different way than the standard way, you can
          directly access the Item and File objects for each attachment object */
      ?>
        <?php foreach ($attachments as $attachment): ?>
            <div class="sp-slide">
                    <?php
                    //attachment objects: items the user has attached to this block
                    $item = $attachment->getItem();
                    $file = $attachment->getFile();
                    ?>

      <!-- Added this check to remove error with metadata(NULL) -AM 7/22/16-->
                    <?php if (isset($item)): ?>

                      <?php if ($description = metadata($item, array('Dublin Core', 'Title'), array('no_escape' => true))): ?>
                          <?php $altText =  $description; ?>
                      <?php endif;
                          /*file_markup($files, $props = array(), $wrapperAttributes = array('class' => 'item-file'))
                          file_markup - Get HTML for a set of files.
                          Returns: string HTML
                          $props - Properties to customize display for different file types.
                              In this case, $props specifies the imageSize, linkToFile and imgAttributes for the HTML
                          */
                          ?>
                          <?php echo file_markup($file, array('imageSize'=>$size,'linkToFile'=>false, 'imgAttributes'=>array('class' => "sp-image", 'alt' =>  "$altText", 'title' => metadata($item, array("Dublin Core", "Title"))))); ?>

                          <?php echo file_markup($file, array('imageSize'=>'square_thumbnail','linkToFile'=>false, 'imgAttributes'=>array('class' => "sp-thumbnail", 'alt' =>  "$altText", 'title' => metadata($item, array("Dublin Core", "Title"))))); ?>
                      <?php //if there is a caption, then a caption title is displayed from the exhibit_builder_link_to_exhibit_item
                            //and then the caption stored in $attachment['caption'] is printed
                      ?>
                      <?php if($attachment['caption']): ?>
                          <div class="sp-layer sp-white sp-padding" data-width="100%" data-position="bottomLeft" data-vertical="0" data-show-transition="up" data-hide-transition="down">
                              <span class="caption-title"><?php echo exhibit_builder_link_to_exhibit_item($description, array(), $item); ?></span>
                              <?php echo $attachment['caption']; ?>
                          </div>
                      <?php endif; ?>

                    <?php endif; ?>

                    <!-- <?php// echo file_markup($file, array('imageSize'=>'thumbnail','linkToFile'=>false, 'imgAttributes'=>array('alt' =>  "$altText", 'class' => 'sp-thumbnail', 'title' => metadata($item, array("Dublin Core", "Title"))))); ?> -->

            </div>
        <?php endforeach; ?>

    </div>

</div>
<div class="exhibit-page-text">
    <?php echo $text; ?>
</div>
