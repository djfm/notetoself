<section 
	id="notetoself"
	class="page-product-box"
	data-update-controller-url="{$notetoself_update_controller_url}"
	data-id-product="{$notetoself_id_product}"
>
	<h3 class="page-product-heading">{l s='My Personal Notes' mod='notetoself'}</h3>
	<textarea id="notetoself-notes" class="form-control">{$notetoself_notes|escape:'htmlall':'UTF-8'}</textarea>
</section>