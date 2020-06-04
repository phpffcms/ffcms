<?php 
    if (!isset($breadcrumbs) || !is_array($breadcrumbs)) {
        return;
    }

    $bread = $this->listing('ol', [
        'class' => 'breadcrumb mt-4 mb-4'
    ]);

    foreach ($breadcrumbs as $k => $v) {
        if (is_string($k)) {
            $bread->li([
                'text' => $k,
                'link' => $v
            ], ['class' => 'breadcrumb-item']);
        } else {
            $bread->li($v, ['class' => 'breadcrumb-item']);
        }
    }
    
    echo $bread->display();
?>