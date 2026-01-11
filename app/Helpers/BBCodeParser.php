<?php

namespace App\Helpers;

use Exception;

class BBCodeParser
{
	private static $text_tags = [
		'linebreak' => [
			'bbcode' => '/\r\n/',
			'html' => '<br>',
			'content' => '',
		],
		'more' => [
			'bbcode' => '/\[more\]/si',
			'html' => '',
			'content' => '',
		],
		'bold' => [
			'bbcode' => '/\[b\](.+?)\[\/b\]/si',
			'html' => '<b>$1</b>',
			'content' => '$1',
		],
		'italic' => [
			'bbcode' => '/\[i\](.+?)\[\/i\]/si',
			'html' => '<i>$1</i>',
			'content' => '$1',
		],
		'underline' => [
			'bbcode' => '/\[u\](.+?)\[\/u\]/si',
			'html' => '<u>$1</u>',
			'content' => '$1',
		],
		'strike' => [
			'bbcode' => '/\[s\](.+?)\[\/s\]/si',
			'html' => '<strike>$1</strike>',
			'content' => '$1',
		],
		'color' => [
			'bbcode' => '/\[color=(#?[A-z0-9]+)\](.+?)\[\/color\]/si',
			'html' => '<span style="color:$1;">$2</span>',
			'content' => '$2',
		],
		'size' => [
			'bbcode' => '/\[size=([1-7])\](.+?)\[\/size\]/si',
			'html' => '<span class="fsize_$1">$2</span>', // change this from depreciated font html at some point
			'content' => '$2',
		],
		'font' => [
			'bbcode' => '/\[font=(.+)\](.+?)\[\/font\]/si',
			'html' => '<span style="font-family:\'$1\'">$2</span>',
			'content' => '$2',
		],
		'left_align' => [
			'bbcode' => '/\[left\](.+?)\[\/left\](?:\<br\>)?/si',
			'html' => '<div class="text-left">$1</div>',
			'content' => '$1',
		],
		'right_align' => [
			'bbcode' => '/\[right\](.+?)\[\/right\](?:\<br\>)?/si',
			'html' => '<div class="text-right">$1</div>',
			'content' => '$1',
		],
		'center_align' => [
			'bbcode' => '/\[center\](.+?)\[\/center\](?:\<br\>)?/si',
			'html' => '<div class="text-center">$1</div>',
			'content' => '$1',
		],
		'justify_align' => [
			'bbcode' => '/\[justify\](.+?)\[\/justify\](?:\<br\>)?/si',
			'html' => '<div class="text-justify">$1</div>',
			'content' => '$1',
		],
		'image' => [
			'bbcode' => '/\[img\](https?:\/\/(?:[a-z0-9\-]+\.)+[a-z]{2,6}(?:\/[^\/#?]+)+\.(?:jpe?g|gif|png|php)(?:\?[A-z0-9=&\.]+)?)\[\/img\]/si',
			'html' => '<img src="$1">',
			'content' => '$1',
		],
		'image_resize' => [
			'bbcode' => '/\[img=([1-9][0-9]*)x([1-9][0-9]*)\](https?:\/\/(?:[a-z0-9\-]+\.)+[a-z]{2,6}(?:\/[^\/#?]+)+\.(?:jpe?g|gif|png|php)(?:\?[A-z0-9=&\.]+)?)\[\/img\]/si',
			'html' => '<img src="$3" width="$1" height="$2">',
			'content' => '$3',
		],
		'link' => [
			'bbcode' => '/\[url\](https?:\/\/(?:[a-z0-9\-]+\.)+[a-z]{2,6}(?:[a-z0-9~_:=&\/\.\#\+\%\-\(\)\?]+)?)\[\/url\]/si',
			'html' => '<a href="$1">$1</a>',
			'content' => '$1',
		],
		'link_text' => [
			'bbcode' => '/\[url=(https?:\/\/(?:[a-z0-9\-]+\.)+[a-z]{2,6}(?:[a-z0-9~_:=&\/\.\#\+\%\-\(\)\?]+)?)\](.+?)\[\/url\]/si',
			'html' => '<a href="$1">$2</a>',
			'content' => '$2',
		],
		'subscript' => [
			'bbcode' => '/\[sub\](.+?)\[\/sub\]/si',
			'html' => '<sub>$1</sub>',
			'content' => '$1',
		],
		'superscript' => [
			'bbcode' => '/\[sup\](.+?)\[\/sup\]/si',
			'html' => '<sup>$1</sup>',
			'content' => '$1',
		],
		'unordered_list' => [
			'bbcode' => '/\[list\](.+?)\[\/list\]/si',
			'html' => '<ul>$1</ul>',
			'content' => '$1',
		],
		'ordered_list' => [
			'bbcode' => '/\[list=([1|a|i])\](.+?)\[\/list\]/si',
			'html' => '<ol type="$1">$2</ol>',
			'content' => '$2',
		],
		'list_item' => [
			'bbcode' => '/\[\*\](.+)/si',
			'html' => '<li>$1</li>',
			'content' => '$1',
		],
		'horizontal_rule' => [
			'bbcode' => '/\[hr\](?:\<br\>)?/si',
			'html' => '<hr>',
			'content' => '',
		],
		'indent_paragraph' => [
			'bbcode' => '/\[indent\](.+?)\[\/indent\](?:\<br\>)?/si',
			'html' => '<div class="bbc_indent">$1</div>',
			'content' => '$1',
		],
		'indent_line' => [
			'bbcode' => '/\[indented\]/si',
			'html' => '<span class="bbc_indent"></span>',
			'content' => '',
		],
		'code' => [ // Keep this at the bottom!
			'bbcode' => '/\[code\](.+?)\[\/code\](?:\<br\>)?/si',
			'html' => '<code>$1</code>',
			'content' => '$1',
		],
	];




	public static function parse($content) {
		$content = str_replace('<', '&lt;', $content);

		$code_blocks = [];
		$code_parse = self::$text_tags['code'];
		while(preg_match($code_parse['bbcode'], $content)) {
			$content = preg_replace_callback($code_parse['bbcode'], function($matches) use (&$code_blocks, $code_parse) {
				$code_id = count($code_blocks);
				$code_blocks[] = $matches[0];
				$replace = '<CODE_'.$code_id.'>';
				return preg_replace($code_parse['bbcode'], $replace, $matches[0]);
			}, $content);
		}

		foreach(self::$text_tags as $type => $parser) {
			if($type == 'code' && count($code_blocks)) {
				foreach($code_blocks as $id => $code) {
					$content = str_replace('<CODE_'.$id.'>', $code, $content);
				}
			}

			$content = self::replaceCode($content, $parser);
		}

		return $content;
	}


	private static function replaceCode($content, array $parser) {
		if(array_key_exists('bbcode', $parser) && array_key_exists('html', $parser)) {
			while(preg_match($parser['bbcode'], $content)) {
				$content = preg_replace($parser['bbcode'], $parser['html'], $content);
			}
		}

		return $content;
	}
}