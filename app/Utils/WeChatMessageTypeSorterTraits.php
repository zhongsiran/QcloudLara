<?php

namespace App\Utils;

trait WeChatMessageTypeSorterTraits 
{
    public function message_type_sorter_daily (array $message) 
    {
        switch ($message['MsgType']) {
            case 'event':
            return '收到事件消息';
            break;

            case 'text':
            $result = $this->handle_text_message($message);
            return $result;
            break;

            case 'image':
            // return '收到图片消息';
            return $this->handle_image_message($message);
            break;

            case 'voice':
            return '收到语音消息';
            break;

            case 'video':
            return '收到视频消息';
            break;

            case 'location':
            $result = $this->handle_location_message($message);
            return $result ?? '收到坐标消息';
            break;

            case 'link':
            return '收到链接消息';
            break;

            case 'file':
            return '收到文件消息';
            break;
            // ... 其它消息
            default:
            return '收到其它消息';
            break;
        }
    }

    public function message_type_sorter_scanning (array $message) 
    {
        switch ($message['MsgType']) {
            case 'text':
            // $result = $this->handle_text_message_scanning($message);
            $result = 'scanning';
            return $result;
            break;

            case 'image':
            // return '收到图片消息';
            return $this->handle_image_message_scanning($message);
            break;

            default:
            return '扫描模式只接收图片和文字信息';
            break;
        }
    }

    public function message_type_sorter_special_action(array $message)
    {
        switch ($message['MsgType']) {
            case 'event':
            return '收到事件消息';
            break;

            case 'text':
            $result = $this->handle_text_message_special_action($message);
            return $result;
            break;

            case 'image':
            // return '收到图片消息';
            return $this->handle_image_message_special_action($message);
            break;

            case 'voice':
            return '收到语音消息';
            break;

            case 'video':
            return '收到视频消息';
            break;

            case 'location':
            $result = $this->handle_location_message($message);
            return $result ?? '收到坐标消息但无法处理';
            break;

            case 'link':
            return '收到链接消息';
            break;

            case 'file':
            return '收到文件消息';
            break;
            // ... 其它消息
            default:
            return '收到其它消息';
            break;
        }
    }
}
