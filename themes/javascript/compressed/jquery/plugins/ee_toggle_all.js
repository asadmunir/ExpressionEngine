/*!
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2012, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.3
 * @filesource
 */

$.fn.toggle_all=function(){function g(a){var b=a.find("tbody tr").get();a.data("table_config")&&a.bind("tableupdate",function(){b=a.table("get_current_data").html_rows;a.find("input:checkbox").prop("checked",!1)});this.getColumn=function(a){return $.map(b,function(b){return b.cells[a]})}}return this.each(function(){var a=$(this),b={},e=new g(a);a.find("th").has("input:checkbox").each(function(){var a=this.cellIndex,c=$(this).find(":checkbox");$(this).click(function(b){var d=c.prop("checked");b.target!=
c.get(0)&&(d=!d,c.prop("checked",d));b=e.getColumn(a);$(b).find(":checkbox").prop("checked",d)});b[a]=c});a.delegate("td","click",function(a){var c=this.cellIndex,f=!0;if(!b[c]||!$(a.target).is(":checkbox"))return!0;if(!a.target.checked)return b[c].prop("checked",!1),!0;$.each(e.getColumn(c),function(){if(!$(this).find(":checkbox").prop("checked"))return f=!1});b[c].prop("checked",f)})})};
