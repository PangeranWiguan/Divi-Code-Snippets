# CSS Responsive Animated Accordion (FOR USE WITH DIVI by Elegant Themes)

A Pen created on CodePen.io. Original URL: [https://codepen.io/PangeranWiguan/pen/qBLaOyL](https://codepen.io/PangeranWiguan/pen/qBLaOyL).

My use case scenario was to hide content while still using DIVI Module.

Any module in between this code will be able to be hidden by the accordion spoiler block.


# HTML Example

Maybe you can implement it better?

Click here to view how it look like in html: [Original Code](/DIVI%20Accordion%20Modular%20Code/original-code/index.html)

This one is extracted from Divi code to html: [Divi Spoiler](/DIVI%20Accordion%20Modular%20Code/Divi%20Spoiler.html)

But below is how I implement it to be use to Divi.

# Usage - How To Use

This is an image on how the code should be implemented inside DiVi by using Code Module.

![Divi Code Module Structure](/DIVI%20Accordion%20Modular%20Code/img/divi-code-structure.png)

All the reference code is availabe at Divi Code Folder here: [Divi Code](/DIVI%20Accordion%20Modular%20Code/Divi%20Code/)

1. Use Divi Code module and paste the code below.

```html
<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
```

This is how it look like.
I then change the Admin Label for ease of management later days if I want to change anything.

![Spoiler JS](/DIVI%20Accordion%20Modular%20Code/img/Spoiler%20JS.png)

2. Add another code module and then paste these Spoiler Reset CSS.

Do not forget to change the Admin Label to Spoiler Reset CSS for easy maintenance.

If you want to change the style of the accordion, just change this code module without touching others.

```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
```

3. Spoiler Style CSS is available here.
[Spoiler Style CSS](/DIVI%20Accordion%20Modular%20Code/Divi%20Code/3.%20style.css)

Add another Divi Code module and paste it there like this.

Don't forget to type the `<style></style>` code element.

```html
<style>
	/* Paste The CSS Code Here */
</style>
```

4. Add another code module, change the admin label to `Spoiler Start` or anything you like.

Paste the code from Divi Code [4. Spoiler Start.html](/DIVI%20Accordion%20Modular%20Code/Divi%20Code/4.%20Spoiler%20Start.html)

Please note that this code is not a complete html, because we want to cancel the Divi auto generated code when you inser anything using the Divi module.

```html
<!--Paste From Here 1 -->
				<!-- Spoiler Start -->

				<div class="accordion">
					<dl>
					  <!-- Loop Starts Here -->
					  <dt>
					    <a href="#accordion3" aria-expanded="false" aria-controls="accordion3" class="accordion-title accordionTitle js-accordionTrigger">
					      Spoiler
					    </a>
					  </dt>
					  <dd class="accordion-content accordionItem is-collapsed" id="accordion3" aria-hidden="true">
					    
					    <!-- Content Start Here -->

					    <div id="canceling-div-1">
						<div id="canceling-div-2">
					    <!-- End Paste From Here 1 -->
```

Add another module of the ending code.

```html
<!--Paste From Here 2 -->
</div> <!--End et_pb_code-->
</div> <!--End et_pb_code_inner-->
<!-- Content End Here -->


</dd>
<!-- Loop Ends Here-->
</dl>
</div>
<!-- Spoiler End -->

</div> <!-- Close div from et_pb_module-->
</div> <!-- Close div from et_pb_module-->
<!-- End Paste From Here 2 -->
```

5. Lastly add another code module and change the Admin Label to `Spoiler Script.JS`

Just like our `Step 3`, copy the whole [6. script.js](/DIVI%20Accordion%20Modular%20Code/Divi%20Code/6.%20script.js) but don't forget to add `<script></script>` tag element like below.

```html
<script>
	<!-- Paste the script.js script here. -->
</script>
```

6. The setup is done. Now you can add any module in between the `Spoiler Start` and `Spoiler End`