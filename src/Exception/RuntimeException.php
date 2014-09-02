<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Exception;

/**
 * Exception thrown if an error which can only be found on runtime occurs
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class RuntimeException extends \RuntimeException implements \hanneskod\classtools\Exception
{
}
