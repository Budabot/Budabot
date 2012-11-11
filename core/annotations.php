<?php

class Channels extends Annotation {

}

class Description extends Annotation {

}

class DefaultStatus extends Annotation {

}

class Visibility extends Annotation {

}

class Type extends Annotation {

}

class Inject extends Annotation {

}

class Matches extends Annotation {

}

class Options extends Annotation {

}

class Intoptions extends Annotation {

}

class Instance extends Annotation {

}

class Setup extends Annotation {

}

class Setting extends Annotation {

}

class Command extends Annotation {

}

class DefineCommand extends Annotation {
	public $command;
	public $channels;
	public $accessLevel;
	public $description;
	public $help;
	public $defaultStatus;
	public $alias;
}

class HandlesCommand extends Annotation {

}

class Event extends Annotation {

}

class Help extends Annotation {

}

class AccessLevel extends Annotation {

}

?>