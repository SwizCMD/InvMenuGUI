<?php

declare(strict_types=1);

namespace srshull\\invmenugui;

/** Explicit callback decision. */
enum TransactionDecision{
    case ALLOW;
    case CANCEL;
}
