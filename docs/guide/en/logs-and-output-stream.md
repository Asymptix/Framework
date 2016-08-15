Logs & OutputStream
---

You can use __OutputStream__ class to output debug information to the browsers output.

```php
use Asymptix\core\OutputStream;

// Start output
OutputStream::start();

// Simple text line
OutputStream::output("Hello, world!\n");
OutputStream::line("Hello, world!");
OutputStream::line("Hello, world!");

// Empty line
OutputStream::line();

// Log with timestamp
OutputStream::log("Simple default log");
OutputStream::log("Simple log with time format", "\(H:i:s\)");
OutputStream::log("Simple log with time {{time}} label");
OutputStream::log("Simple log with time {{time}} label and format", "\(H:i:s\)");
OutputStream::log("Log with few time {{time}} labels {{time}}");

OutputStream::line();

// Log colored by message type
OutputStream::msg(OutputStream::MSG_INFO, "Info message");
OutputStream::msg(OutputStream::MSG_DEBUG, "Debug message with time format", "\(H:i:s\)");
OutputStream::msg(OutputStream::MSG_SUCCESS, "Success message with time {{time}} label");
OutputStream::msg(OutputStream::MSG_WARNING, "Warning message with time {{time}} label and format", "\(H:i:s\)");
OutputStream::msg(OutputStream::MSG_ERROR, "Default Error message");

// Close output
OutputStream::close();
```

Also you can use enhanced log functionality of the __Logger__:

```php
use Asymptix\tools\logging\Logger;

// Log to the browser output with OutputStream
$logger = new Logger(Logger::TO_OUTPUT_STREAM);

$logger->log(Logger::LOG_INFO, "Hello, world!");
$logger->log(Logger::LOG_INFO, "Hello, world {{time}}!");
$logger->log(Logger::LOG_INFO, "Hello, world {{time}}!", "(Y)", time());
$logger->log(Logger::LOG_DEBUG, "Hello, world!");
$logger->log(Logger::LOG_SUCCESS, "Hello, world!");
$logger->log(Logger::LOG_WARNING, "Hello, world!");
$logger->log(Logger::LOG_ERROR, "Hello, world!");

$logger->close();

// Log to the file
$logger = new Logger(Logger::TO_FILE, "log.txt");

$logger->log(Logger::LOG_INFO, "Hello, world!");
$logger->log(Logger::LOG_INFO, "Hello, world {{time}}!");
$logger->log(Logger::LOG_INFO, "Hello, world {{time}}!", "(Y)", time());
$logger->log(Logger::LOG_DEBUG, "Hello, world!");
$logger->log(Logger::LOG_SUCCESS, "Hello, world!");
$logger->log(Logger::LOG_WARNING, "Hello, world!");
$logger->log(Logger::LOG_ERROR, "Hello, world!");

$logger->close();

// Log to the DB with using bean class extended from __\Asymptix\db\DBObject__
$logger = new Logger(Logger::TO_DB, new Asymptix\tools\logging\LogDBObject);

$logger->log(Logger::LOG_INFO, "Hello, world!");
$logger->log(Logger::LOG_INFO, "Hello, world {{time}}!");
$logger->log(Logger::LOG_INFO, "Hello, world {{time}}!", "(Y)", time());
$logger->log(Logger::LOG_DEBUG, "Hello, world!");
$logger->log(Logger::LOG_SUCCESS, "Hello, world!");
$logger->log(Logger::LOG_WARNING, "Hello, world!");
$logger->log(Logger::LOG_ERROR, "Hello, world!");

$logger->close();
```