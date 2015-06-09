<?php

require_once("./core/OutputStream.php");

OutputStream::start();

OutputStream::output("Simpel text \n");
OutputStream::line("Simpel text line");
OutputStream::line("Simpel text line");

OutputStream::line();

OutputStream::log("Simple default log");
OutputStream::log("Simple log with time format", "\(H:i:s\)");
OutputStream::log("Simple log with time {{time}} label");
OutputStream::log("Simple log with time {{time}} label and format", "\(H:i:s\)");
OutputStream::log("Log with few time {{time}} labels {{time}}");

OutputStream::line();

OutputStream::msg(OutputStream::MSG_INFO, "Info message");
OutputStream::msg(OutputStream::MSG_DEBUG, "Debug message with time format", "\(H:i:s\)");
OutputStream::msg(OutputStream::MSG_SUCCESS, "Success message with time {{time}} label");
OutputStream::msg(OutputStream::MSG_WARNING, "Warning message with time {{time}} label and format", "\(H:i:s\)");
OutputStream::msg(OutputStream::MSG_ERROR, "Default Error message");

OutputStream::close();

?>