# LIN 协议包 — 车身总线，19200 bps UART，主从模式

> [English](README.en.md)

LIN (Local Interconnect Network) 汽车车身总线协议，19200 bps UART 串口通信，主从模式。支持 LIN PID 帧读写和校验。

## 安装

```bash
composer require erikwang2013/industrial-protocols-lin
```

## 架构

LinDriver（串口 UART）→ LinFrame 帧编解码。支持 LIN PID 寻址、增强校验和、帧读写。

## 功能

LIN 帧读写（PID 寻址 0x00-0x3F）、增强校验和校验、LinException 异常

## 使用说明

```php
$conn = $kernel->getConnectionManager()->connect('lin-device');
$conn->read('0x3C');  // 按 LIN PID 读取
```

## 配置示例

```php
'devices' => [
    'lin-device' => [
        'protocol' => 'lin', 'variant' => 'master',
        'device' => '/dev/ttyUSB3', 'baud_rate' => 19200,
        'timeout' => 3000,
    ],
],
```

## 兼容框架

Laravel / Webman / Hyperf / ThinkPHP / Yii2 / Yii3 / Plain PHP

## 系统要求

- PHP >= 8.1
- LIN 串口适配器（USB/UART）
- erikwang2013/industrial-protocols-kernel

## License

MIT — Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
