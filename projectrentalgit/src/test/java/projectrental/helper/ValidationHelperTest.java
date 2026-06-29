package projectrental.helper;

import org.junit.jupiter.api.Test;
import static org.junit.jupiter.api.Assertions.*;

public class ValidationHelperTest {

    @Test
    public void testIsAnyEmpty_AllNotEmpty() {
        assertFalse(ValidationHelper.isAnyEmpty("test", "user", "password123"));
    }

    @Test
    public void testIsAnyEmpty_SomeEmpty() {
        assertTrue(ValidationHelper.isAnyEmpty("test", "", "password123"));
    }

    @Test
    public void testIsAnyEmpty_AllEmpty() {
        assertTrue(ValidationHelper.isAnyEmpty("", "", ""));
    }

    @Test
    public void testIsAnyEmpty_NullValue() {
        assertTrue(ValidationHelper.isAnyEmpty("test", null, "password123"));
    }

    @Test
    public void testIsAnyEmpty_OnlyWhitespace() {
        assertTrue(ValidationHelper.isAnyEmpty("  ", "valid", "valid"));
    }

    @Test
    public void testIsAnyEmpty_NullArray() {
        assertTrue(ValidationHelper.isAnyEmpty((String[]) null));
    }

    @Test
    public void testIsValidEmail_Valid() {
        assertTrue(ValidationHelper.isValidEmail("admin@gmail.com"));
        assertTrue(ValidationHelper.isValidEmail("user.test@drivora.id"));
    }

    @Test
    public void testIsValidEmail_MissingAtSign() {
        assertFalse(ValidationHelper.isValidEmail("admingmail.com"));
    }

    @Test
    public void testIsValidEmail_MissingDot() {
        assertFalse(ValidationHelper.isValidEmail("admin@gmailcom"));
    }

    @Test
    public void testIsValidEmail_NullEmail() {
        assertFalse(ValidationHelper.isValidEmail(null));
    }

    @Test
    public void testIsValidEmail_EmptyEmail() {
        assertFalse(ValidationHelper.isValidEmail(""));
    }
}
