<?php

class Bert_Types
{
	const SMALL_INT = 97;
	const INT = 98;
	const SMALL_BIGNUM = 110;
	const LARGE_BIGNUM = 111;
	const FLOAT = 99;
	const ATOM = 100;
	const SMALL_TUPLE = 104;
	const LARGE_TUPLE = 105;
	const NIL = 106;
	const STRING = 107;
	const LISTTYPE = 108;
	const BIN = 109;
	const FUN = 117;
	const NEW_FUN = 112;
	const MAGIC = 131;
	const MAX_INT = 134217727; // (1 << 27) - 1
	const MIN_INT = -134217728; // -(1 << 27)
}
