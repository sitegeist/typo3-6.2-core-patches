===================================================
Breaking: #75355 - FlexForm-related options removed
===================================================

Description
===========

The following options regarding FlexForm handling have been removed.

* $TYPO3_CONF_VARS[BE][niceFlexFormXMLtags]
* $TYPO3_CONF_VARS[BE][compactFlexFormXML]

Storing FlexForms in the database via the TYPO3 Core API does not compact the XML files anymore (it now always
uses 4 spaces for indentation inside the XML) and also always uses meaningful tags so it can be validated with DTDs.


Impact
======

Having the options set will result in new/updated FlexForm data being written
with spaces instead of tabs and with meaningful tags while keeping full backwards compatibility when reading.

This was the default for new installations already, but introduced due to legacy installations (pre 4.0) that dealt with
FlexForms back in 2004.


Affected Installations
======================

Any installation having these properties set in their LocalConfiguration.php.

Any extension evaluating these parameters on its own.

.. index:: flexform
