<?php
echo $this->partial('layout', ['content' => $this->partial($page, $page_data ?? [])]);