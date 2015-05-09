<?php


/** 
 * @package		Rokin Gallery
 * @author    	Pageworks http://www.pageworks.nl
 * @copyright	Copyright (c) 2006 - 2010 Pageworks. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

if(!(defined('_VALID_MOS') || defined( '_JEXEC' ))){
	die( 'Restricted access' );
}

class pager{
	var $version = "1.1.2";
	var $release_date = "1-5-2012";
	var $record_low;
	var $record_high;
	var $preNavUrl;
	var $items_per_page= 20;
	var $page_id = "page";
	var $cur_page = null; 
	var $totalItems;
	var $total_pages;
	var $limit;
	
	function Pager($totalItems, $items_per_page=20){
		$this->totalItems = $totalItems;
		$this->items_per_page = $items_per_page;
	}
	
	function version(){
		echo $version;
	}
	
	function getCurrentPage(){
		if($this->cur_page==null){
			if(isset($_REQUEST[$this->page_id]))
				$cur_page = $_REQUEST[$this->page_id];
			else
				$cur_page = 1;
					
			$this->cur_page = $cur_page;
		}
		return $this->cur_page;
	}
	
	/**
	 * return an sql limit string
	 *
	 * @param unknown_type $total_records, the total records to be pages about
	 * @return unknown
	 */
	function getLimit(){
		$cur_page = $this->getCurrentPage();
		
		if($this->totalItems  < $this->items_per_page){
			$record_low = 0;
			$record_high = $this->totalItems;
		}else{
			$record_low = ($cur_page * $this->items_per_page) - $this->items_per_page ;
			if(($record_low + $this->items_per_page) > $this->totalItems){
				 $record_high = $this->totalItems;
			}else{
				$record_high = $record_low +  $this->items_per_page;
			}
			
		}	
		
		$this->record_low = $record_low;
		$this->record_high = $record_high;
		
		$remainrec = $this->totalItems % $this->items_per_page; //berekend het restant // stel 79 rcds / 20 = 19
		$this->total_pages = floor($this->totalItems / $this->items_per_page); //aantal pagina's		
		if ($remainrec > 0) { //kijk of restant groter is dan 0, zo ja tel 1 pagina op
			$this->total_pages++;
		}	
		
		$this->limit = " LIMIT " . $this->record_low ."," . $this->items_per_page;

		//return the sql limit
		 return $this->limit;
	}
	
	/**
	 * returns the start index of the selectedpage
	 *
	 * @param unknown_type $total_records
	 * @return unknown
	 */
	function getFirst(){
		$this->getLimit();
		return $this->record_low;		
	}
	
	/**
	 * returns the end index of the selectedpage
	 *
	 * @param unknown_type $total_records
	 * @return unknown
	 */
	function getLast(){
		$this->getLimit();
		return $this->record_high;
	}

	function render(){
		$limit = $this->getLimit();
		
		$url = $this->curPageURL();
		//echo $url;
		
		//retrieve the current url and remove the page declaration from the url
		//$url = selfURL();
		if(instr($url, '?' . $this->page_id)) {				
			//refactor the url
			$url =  substr($url,0, strpos($url,"?"  . $this->page_id .  "="));	
		}
		if(instr($url, '&' . $this->page_id)) {				
			//refactor the url
			$url =  substr($url,0, strpos($url,"&". $this->page_id. "="));	
		}
		
		$preNavUrl = $this->urlGlue($url);
	
		
	?>
		<form method="POST" name="pwpager">
			<table class="cstp-tabular-header" width="100%" cellpadding="0" cellspacing="0"  >
			<tr>
				<td class="table-paging">
					<?php  echo  $this->record_low + 1 . ' ' . JText::_('to') . ' ' . $this->record_high . ' ' . JText::_('of') .' ' . $this->totalItems; ?>
				</td>

				<td class="table-paging" >				 
					<?php  if($this->cur_page > 1){?>
					<a class="button" href="<?php  echo $url . $preNavUrl . $this->page_id. "=" . ($this->cur_page - 1); ?>"><?php echo JText::_("PREV");?></a>
					<?php  } ?>
					<?php echo JText::_("PAGE");?>&nbsp;			
					<select name="<?php  echo $this->page_id;  ?>" style="width:80px;text-align: center"  onchange="document.pwpager.submit();">
					<?php 
						for($i=1 ; $i <= $this->total_pages ;$i++ ){
							if($i==$this->cur_page){
								echo "<option selected='selected' >$i</option>";
							}else{
								echo "<option>$i</option>";
							}
						}
					?>
					</select>
					<?php 
					if($this->cur_page < $this->total_pages){?>
					<a class="button" href="<?php  echo $url . $preNavUrl . $this->page_id. "=" . ($this->cur_page + 1); ?>"><?php echo JText::_("NEXT");?> </a>			
					<?php  } ?>
				</td>
			</tr>
			
			</table>
		</form>		
	<?php 
		//return the sql limit
		 return $limit;
		 
	}
}


/**
* This pager does not have a form of its own but uses an already available form making it easy to add search capabilities
*/
class FormlessPager{
	var $version = "1.0.0";
	var $release_date = "1-12-2012";
	var $record_low;
	var $record_high;
	var $preNavUrl;
	var $items_per_page= 20;
	var $page_id = "page";
	var $cur_page = null; 
	var $totalItems;
	var $total_pages;
	var $option;
	var $unit;
	var $act;
	var $form;
	
	var $limit;
	
	function FormlessPager($totalItems, $items_per_page=20){
		$this->totalItems = $totalItems;
		$this->items_per_page = $items_per_page;
	}
	
	function version(){
		echo $version;
	}
	
	function getCurrentPage(){
		if($this->cur_page==null){
			if(isset($_REQUEST[$this->page_id]))
				$cur_page = $_REQUEST[$this->page_id];
			else
				$cur_page = 1;
					
			$this->cur_page = $cur_page;
		}
		return $this->cur_page;
	}
	
	/**
	 * return an sql limit string
	 *
	 * @param unknown_type $total_records, the total records to be pages about
	 * @return unknown
	 */
	function getLimit(){
		$cur_page = $this->getCurrentPage();
		
		if($this->totalItems  < $this->items_per_page){
			$record_low = 0;
			$record_high = $this->totalItems;
		}else{
			$record_low = ($cur_page * $this->items_per_page) - $this->items_per_page ;
			if(($record_low + $this->items_per_page) > $this->totalItems){
				 $record_high = $this->totalItems;
			}else{
				$record_high = $record_low +  $this->items_per_page;
			}
			
		}	
		
		$this->record_low = $record_low;
		$this->record_high = $record_high;
		
		$remainrec = $this->totalItems % $this->items_per_page; //berekend het restant // stel 79 rcds / 20 = 19
		$this->total_pages = floor($this->totalItems / $this->items_per_page); //aantal pagina's		
		if ($remainrec > 0) { //kijk of restant groter is dan 0, zo ja tel 1 pagina op
			$this->total_pages++;
		}	
		
		$this->limit = " LIMIT " . $this->record_low ."," . $this->items_per_page;

		//return the sql limit
		 return $this->limit;
	}
        
        
        function curPageURL() {
                $pageURL = 'http';
                if(isset($_SERVER["HTTPS"]))
                        if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
                $pageURL .= "://";
                if ($_SERVER["SERVER_PORT"] != "80") {
                        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
                } else {
                        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
                }
                return $pageURL;
        }
        
        

        /**
         * Returns true if the occurence if the needle is within the haystack
         *
         * @param unknown_type $haystack
         * @param unknown_type $needle
         * @return unknown
         */
        function instr($haystack, $needle){
                if(stristr($haystack, $needle)===false){
                        return false;	
                }else{
                        return true;
                }

        }

        /**
         * returns the appropiate concat charachter based upon the current url
         * if the url contains index.php?blabla it returns & else it returns ?
         *
         * @param unknown_type $url
         * @return unknown
         */
        function urlGlue($url){
                if($this->instr($url, "?")) {
                        return "&";
                }else{
                        return  "?";
                }
        }
   
	/**
	 * returns the start index of the selectedpage
	 *
	 * @param unknown_type $total_records
	 * @return unknown
	 */
	function getFirst(){
		$this->getLimit();
		return $this->record_low;		
	}
	
	/**
	 * returns the end index of the selectedpage
	 *
	 * @param unknown_type $total_records
	 * @return unknown
	 */
	function getLast(){
		$this->getLimit();
		return $this->record_high;
	}

	function render(){
		$limit = $this->getLimit();
		
		$url = $this->curPageURL();
		
		//retrieve the current url and remove the page declaration from the url
		//$url = selfURL();
		$url = $this->preNavUrl;
		if($this->instr($url, '?' . $this->page_id)) {				
			//refactor the url
			$url =  substr($url,0, strpos($url,"?"  . $this->page_id .  "="));	
		}
		if($this->instr($url, '&' . $this->page_id)) {				
			//refactor the url
			$url =  substr($url,0, strpos($url,"&". $this->page_id. "="));	
		}
		$preNavUrl = $this->urlGlue($url);
	
		
	?>		
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" >
			<input type="hidden" name="unit" value="<?php echo $this->unit; ?>" >
			<input type="hidden" name="act" value="<?php echo $this->act; ?>" >
			<input type="hidden" name="ItemId" value="<?php echo $this->ItemId; ?>" >
			
			<div class="pagination pagination-centered">
                            <ul>
                                <li>   
                                <?php  if($this->cur_page > 1) : ?>
                                        <a href="javascript:document.<?php echo $this->form; ?>.<?php  echo $this->page_id;  ?>.value='<?php echo ($this->cur_page - 1); ?>'; document.<?php echo $this->form; ?>.submit(); "><?php echo JText::_("JPREV");?> </a>
                                  <?php else: ?>
                                        <a href="#" class="disabled"><?php echo JText::_("JPREV");?> </a>
                                  <?php endif; ?>
                                 </li>  
                                  <li ><a href="#">
					<?php  echo  $this->record_low + 1 . ' ' . JText::_('to') . ' ' . $this->record_high . ' ' . JText::_('of') .' ' . $this->totalItems; ?>
                                    </a>
                                  </li>   
                                  <li >
                                  <?php if($this->cur_page < $this->total_pages): ?>
                                            <a href="javascript:document.<?php echo $this->form; ?>.<?php  echo $this->page_id;  ?>.value='<?php echo ($this->cur_page + 1); ?>'; document.<?php echo $this->form; ?>.submit(); "><?php echo JText::_("JNEXT");?> </a>
                                 <?php  else: ?>
                                      <a href="#" class="disabled"><?php echo JText::_("JNEXT");?> </a>
                                  <?php  endif; ?>
                                 
                                  </li>
                                  <li>
                                      
                                <div class="input-prepend input-append">
                                    <span class="add-on">
                                              <?php echo JText::_("PAGE");?>&nbsp;					                                          
                                    </span>
                                            <select class="span2" name="<?php  echo $this->page_id;  ?>" onchange="document.<?php echo $this->form; ?>.submit();">
                                            <?php 
                                                    for($i=1 ; $i <= $this->total_pages ;$i++ ){
                                                            if($i==$this->cur_page){
                                                                    echo "<option selected='selected' >$i</option>";
                                                            }else{
                                                                    echo "<option value='". $i . "'>$i</option>";
                                                            }
                                                    }
                                            ?>
                                            </select>
                                      </div>
                                  </li>
                            </ul>
			</div>
	<?php 
		//return the sql limit
		 return $limit;
		 
	}
}
	